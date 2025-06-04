<?php
namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\Core\Configure;

class NodesController extends AppController
{
  public $allow = ['load', 'sort', 'autolink'];
  public $fieldset = '';

  public function initialize()
  {
    parent::initialize();

    $this->fieldset = __d('be', 'Settings');
  }

  public function create($structure)
  {
    // init
    $this->autoRender = false;
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!')
    ];
    $this->loadModel('Backend.Structures');
    $this->loadModel('Backend.Elements');

    // settings
    $settings = Configure::read('elements');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if (
      $structure &&
      $this->request->is('post') &&
      array_key_exists('element', $this->request->data) &&
      count($this->request->data['element']) == 1
    ) {
      foreach ($this->request->data['element'] as $element) {
        // check element
        try {
          $element = $this->Elements->get($element);
        } catch (RecordNotFoundException $e) {
          $element = false;
        }

        // create
        if ($element) {
          $node = $this->Nodes->newEntity();
          $this->Nodes->patchEntity($node, [
            'structure_id' => $structure['id'],
            'foreign_id' => $element['id'],
            'route' => $this->Nodes->createRoute(),
            'position' => 0,
            'robots_follow' => 1,
            'robots_index' => 1,
            'show_from' => time()
          ]);
          // vd($node);

          if ($result = $this->Nodes->save($node)) {
            // type
            $type = $settings[$element['code']]['translations']['type'];
            

            $ret = [
              'success' => true,
              'node' => [
                'title' => $element['internal'],
                'data' => [
                  'info' => [
                    'id' => $result['id'],
                    'foreign_id' => $result['foreign_id'],
                    'popup' => $result['popup'] ? true : false,
                    'active' => $result['active'] ? true : false,
                    'jump' => $result['jump'] ? true : false,
                    'follow' => $result['robots_follow'] ? true : false,
                    'index' => $result['robots_index'] ? true : false,
                    'from' => $result['show_from'],
                    'to' => $result['show_to'],
                    'route' => $result['route'],
                    'missing' => false
                  ],
                  'element' => [
                    'internal' => $element['internal'],
                    'code' => $element['code'],
                    'type' => $type
                  ],
                  'linkable' => array_key_exists(
                    'linkable',
                    $settings[$element['code']]
                  )
                    ? $settings[$element['code']]['linkable']
                    : true,
                  'type' => 'create'
                ]
              ]
            ];
          }
        }
      }
    }

    echo json_encode($ret);
    exit();
  }

  public function load($structure)
  {
    // init
    $json = [];
    $this->autoRender = false;
    $this->loadModel('Backend.Structures');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if ($structure) {
      $nodes = $this->Nodes
        ->find('threaded')
        ->select()
        ->where(['structure_id =' => $structure['id']])
        ->order(['parent_id' => 'ASC', 'position' => 'ASC'])
        ->hydrate(false)
        ->toArray();
      $this->Nodes->buildTreeJSON($json, $nodes);
    }

    echo json_encode($json);
    exit();
  }

  public function sort($structure)
  {
    if($_SERVER['REMOTE_ADDR'] ==  '31.223.221.124') {
      //var_dump($structure, $this->request->data);
      //die();
    }
    // init
    $success = true;
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!')
    ];
    $this->autoRender = false;
    $this->loadModel('Backend.Structures');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if (
      $structure &&
      $this->request->is('post') &&
      count($this->request->data) > 0
    ) {
      // save
      foreach ($this->request->data as $id => $info) {
        if ($success === true) {
          if($_SERVER['REMOTE_ADDR'] ==  '31.223.221.124') {
            // var_dump($id);die();
          }
          $result = $this->connection->execute(
            "UPDATE `nodes` SET `parent_id` = :parent, `position` = :position WHERE `id` = :id LIMIT 1",
            [
              'parent' => $info['parent'],
              'position' => $info['position'],
              'id' => $id
            ]
          );
          if (!$result) {
            $success = false;
          }
        }
      }
    }

    if ($success) {
      $ret = ['success' => true];
    }

    echo json_encode($ret);
    exit();
  }

  public function toggle($structure)
  {
    // init
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!')
    ];
    $this->autoRender = false;
    $this->loadModel('Backend.Structures');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if (
      $structure &&
      $this->request->is('post') &&
      count($this->request->data) > 0 &&
      array_key_exists('id', $this->request->data) &&
      array_key_exists('field', $this->request->data) &&
      array_key_exists('value', $this->request->data)
    ) {
      // check node
      try {
        $node = $this->Nodes->get($this->request->data['id']);
      } catch (RecordNotFoundException $e) {
        $node = false;
      }

      if ($node) {
        // prepare field
        switch ($this->request->data['field']) {
          case "active":
            $field = 'active';
            break;
          case "jump":
            $field = 'jump';
            break;
          case "index":
            $field = 'robots_index';
            break;
          case "follow":
            $field = 'robots_follow';
            break;
          case "show":
            $field = 'display';
            break;
          default:
            $field = false;
            break;
        }

        // prepare value
        $value = (int) $this->request->data['value'] == 1 ? 0 : 1;

        // save
        if ($field) {
          $this->Nodes->patchEntity($node, [$field => $value]);
          if ($result = $this->Nodes->save($node)) {
            $ret = ['success' => true, 'value' => $value, 'result' => $result];
          }
        }
      }
    }

    echo json_encode($ret);
    exit();
  }

  public function period($structure)
  {
    // init
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!')
    ];
    $this->autoRender = false;
    $this->loadModel('Backend.Structures');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if (
      $structure &&
      $this->request->is('post') &&
      count($this->request->data) > 0 &&
      array_key_exists('id', $this->request->data) &&
      array_key_exists('from', $this->request->data) &&
      array_key_exists('to', $this->request->data)
    ) {
      // check node
      try {
        $node = $this->Nodes->get($this->request->data['id']);
      } catch (RecordNotFoundException $e) {
        $node = false;
      }

      if ($node) {
        // save
        $this->Nodes->patchEntity($node, [
          'show_from' => $this->request->data['from'],
          'show_to' => $this->request->data['to']
        ]);
        if ($result = $this->Nodes->save($node)) {
          $ret = [
            'success' => true,
            'from' => $this->request->data['from'],
            'to' => $this->request->data['to'],
            'result' => $result
          ];
        }
      }
    }

    echo json_encode($ret);
    exit();
  }

  public function delete($structure)
  {
    // init
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!')
    ];
    $this->autoRender = false;
    $this->loadModel('Backend.Structures');

    // check structure
    try {
      $structure = $this->Structures->get($structure);
    } catch (RecordNotFoundException $e) {
      $structure = false;
    }

    if (
      $structure &&
      $this->request->is('post') &&
      count($this->request->data) > 0 &&
      array_key_exists('id', $this->request->data)
    ) {
      if (
        $used = $this->isUsed(
          $this->request->data['id'],
          false,
          ['nodes'],
          false
        )
      ) {
        $ret['msg'] =
          __d('be', 'The node could not be deleted because it is in use!') .
          "\n\n" .
          join("\n", $used);
      } else {
        try {
          $node = $this->Nodes->get($this->request->data['id']);
          if ($this->Nodes->delete($node)) {
            $ret['success'] = true;
          }
        } catch (RecordNotFoundException $e) {
        }
      }
    }

    echo json_encode($ret);
    exit();
  }

  function settings($structure, $id)
  {
    // settings
    $settings = Configure::read('node-settings');
    if (!is_array($settings) || count($settings) < 1) {
      // error
      $this->Flash->error(__d('be', 'No node settings settings available!'));

      // redirect
      return $this->redirect([
        'controller' => 'structures',
        'action' => 'tree',
        $structure
      ]);
    }

    // fieldsets
    $def = false;
    $fieldsets = [$this->fieldset];
    foreach ($settings as $k => $v) {
      if (array_key_exists('fieldset', $v) && $v['fieldset']) {
        if (!in_array($v['fieldset'], $fieldsets)) {
          $fieldsets[] = $v['fieldset'];
        }
      } else {
        $def = true;
      }
    }
    if ($def === false) {
      unset($fieldsets[0]);
    }
    $this->set('dummy', $this->fieldset);
    $this->set('fieldsets', $fieldsets);

    // get first config entry
    $ns = $this->Nodes
      ->find()
      ->where(['id' => $id])
      ->formatResults(function ($results) use ($settings) {
        return $results->map(function ($row) {
          return $this->Nodes->afterFind($row);
        });
      })
      ->first();
    if ($ns == false) {
      // error
      $this->Flash->error(__d('be', 'Node not found!'));

      // redirect
      return $this->redirect([
        'controller' => 'structures',
        'action' => 'tree',
        $structure
      ]);
    }

    // languages
    $languages = [];
    foreach (Configure::read('translations') as $k => $v) {
      if ($v['active']) {
        $languages[$k] = $v;
      }
    }

    // save
    if ($this->request->is(['post', 'put'])) {
      $this->request->data['_mode'] = 'settings';
      $this->Nodes->patchEntity($ns, $this->request->data, [
        'validate' => 'settings'
      ]);
      if ($result = $this->Nodes->save($ns)) {
        $this->Flash->success(__d('be', 'The node settings have been saved.'));
        if ($this->request->params['redirect']) {
          return $this->redirect([
            'controller' => 'structures',
            'action' => 'tree',
            $structure
          ]);
        } else {
          return $this->redirect(['action' => 'settings', $structure, $id]);
        }
      } else {
        $this->Flash->error(__d('be', 'Unable to save node settings!'));
      }
    }
    $this->set('ns', $ns);

    // element name
    $element = $this->connection
      ->execute("SELECT * FROM `elements` WHERE `id` = :id", [
        'id' => $ns->foreign_id
      ])
      ->fetch('assoc');
    if (!is_array($element) || count($element) < 1) {
      $element = ['internal' => ''];
    }

    // menu
    $menu = [
      'left' => [],
      'right' => [
        [
          'type' => 'link',
          'text' => __d('be', 'Back to structure'),
          'url' => [
            'controller' => 'structures',
            'action' => 'tree',
            $structure
          ],
          'attr' => [
            'class' => 'button'
          ]
        ]
      ]
    ];

    $this->set(
      'title',
      __d('be', 'Node settings') . ' <span>' . $element['internal'] . '</span>'
    );
    $this->set('settings', $settings);
    $this->set('languages', $languages);
    $this->set('menu', $menu);
    $this->set('load_editor', true);
    $this->set('load_datepicker', true);
  }

  function autolink($structure_id)
  {
    // init
    $ret = [
      'success' => false,
      'msg' => __d('be', 'An error has occurred, please try again!'),
      'replace' => false
    ];
    $restore = [];
    $this->autoRender = false;

    if (
      $this->request->is('post') &&
      array_key_exists('content', $this->request->data)
    ) {
      // template
      $template = Configure::read('elements.link.editor.template');

      // locale
      $locale = array_key_exists('locale', $this->request->query)
        ? $this->request->query['locale']
        : false;

      // content
      $content = stripslashes(
        html_entity_decode($this->request->data['content'], ENT_COMPAT, 'UTF-8')
      );

      // fetch nodes
      $nodes = $this->connection
        ->execute(
          "SELECT `n`.`id`, `t`.`content` FROM `nodes` as `n` LEFT JOIN `i18n` as `t` ON (`n`.`foreign_id` = `t`.`foreign_key`) WHERE `n`.`structure_id` = :id AND `t`.`field` = :field AND `t`.`locale` = :locale",
          ['id' => $structure_id, 'field' => 'title', 'locale' => $locale]
        )
        ->fetchAll('assoc');

      if (is_array($nodes) && count($nodes) > 0) {
        // crypt existing links
        if (preg_match_all("#<a(.*)>(.*)<\/a>#Ui", $content, $existing)) {
          foreach ($existing[0] as $link) {
            $crypt = md5($link);
            $restore[$crypt] = $link;
            $content = str_replace($link, $crypt, $content);
          }
        }

        // add auto links
        foreach ($nodes as $node) {
          $link = str_replace(
            ['%model', '%code', '%id', '%class', '%title'],
            ['nodes', 'node', $node['id'], 'autolink', $node['content']],
            $template
          );
          $crypt = md5($link);
          $restore[$crypt] = $link;
          $content = str_replace($node['content'], $crypt, $content);
        }

        // restore links
        foreach ($restore as $crypt => $link) {
          $content = str_replace($crypt, $link, $content);
        }

        $ret['content'] = $content;
        $ret['replace'] = true;
      }

      $ret['success'] = true;
    }

    echo json_encode($ret);
    exit();
  }
}
