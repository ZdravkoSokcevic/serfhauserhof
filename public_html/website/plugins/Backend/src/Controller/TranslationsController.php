<?php

namespace Backend\Controller;

use Backend\Controller\AppController;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class TranslationsController extends AppController {

    public $allow = ['init','load','update'];

    public function index($domain = false, $language = false) {

        // init
        $domain = $domain == false ? 'be' : $domain;
        $language = $language == false ? Configure::read('language') : $language;

        // options
        $options = [];
        $fetch = ['' => __d('be', '-- All --')];
        $domains = Configure::read('plugin_i18n');
        foreach($domains as $k => $v){
            if($v['fetch']){
                $fetch[$v['domain']] = $v['name'];
            }
            $options[$v['name']] = [];
            foreach(Configure::read($v['list']) as $_k => $_v){
                if($_v['active']){
                    $options[$v['name']][$v['domain'] . ':' . $_k] = $_v['title'];
                }
            }
        }

        // get translations
        $query = $this->Translations->find('all')->select(['id', 'locale', 'domain', 'fallback', 'translation', 'translated'])->where(['domain' => $domain, 'locale' => $language])->order(['translated' => 'ASC', 'fallback' => 'ASC']);
        $translations = $query->toArray();
        $this->set('translations', $translations);

        // menu
        $menu = [
            'left' => [
                [
                    'type' => 'select',
                    'name' => 'domain_language',
                    'attr' => [
                        'options' => $options,
                        'default' => $domain . ':' . $language,
                        'class' => 'dropdown',
                    ],
                ],
            ],
            'right' => [
                [
                    'type' => 'select',
                    'name' => 'domain',
                    'attr' => [
                        'options' => $fetch,
                        'class' => 'dropdown init',
                    ],
                ],
                [
                    'type' => 'link',
                    'text' => __d('be', 'Fetch translations'),
                    'url' => ['controller' => 'translations', 'action' => 'init'],
                    'attr' => [
                        'class' => 'button init',
                    ],
                ],
                [
                    'show' => __cp(['controller' => 'translations', 'action' => 'import'], $this->request->session()->read('Auth')),
                    'type' => 'icon',
                    'text' => __d('be', 'Import translations'),
                    'url' => ['controller' => 'translations', 'action' => 'import'],
                    'icon' => 'upload',
                    'class' => 'import',
                    'action' => 'select:file',
                    'select' => [
                        'name' => 'import_domain',
                        'attr' => [
                            'options' => $options,
                            'class' => 'dropdown',
                            'escape' => false,
                        ]
                    ],
                    'file' => [
                        'name' => 'import_file',
                        'attr' => [
                            'class' => 'file space',
                            'accept' => '.csv'
                        ]
                    ],
                ],
                [
                    'show' => __cp(['controller' => 'translations', 'action' => 'export'], $this->request->session()->read('Auth')),
                    'type' => 'icon',
                    'text' => __d('be', 'Export translations'),
                    'url' => ['controller' => 'translations', 'action' => 'export'],
                    'icon' => 'download',
                    'class' => 'export',
                    'action' => 'select',
                    'select' => [
                        'name' => 'export_domain',
                        'attr' => [
                            'options' => $options,
                            'class' => 'dropdown',
                            'escape' => false,
                        ]
                    ]
                ],
            ],
        ];

        $this->set('title', __d('be', 'Translations'));
        $this->set('menu', $menu);

    }

    public function init($domain = false){

        // init
        $combinations = [
            'singular' => sampling(['"',"'"], 2),
            'plural' => sampling(['"',"'"], 3),
        ];

        $this->autoRender = false;
        $fetch = Configure::read('plugin_i18n');

        $files = [];
        $paths = [
            CONFIG,
            Configure::read('App.paths.plugins.0')
        ];

        // get files
        foreach($paths as $path){
            $dir = new Folder($path);
            $files = array_merge($files, $dir->findRecursive('.*\.(ctp|php)'));
        }

        // fetch
        foreach($fetch as $key => $i18n){

            if($i18n['fetch'] && ($domain === false || $i18n['domain'] == $domain)){

                // init
                $now = date('Y-m-d H:i:s', time());
                $search = ['\"',"\'"];
                $convert = ['"', "'"];
                $replace = ['%%%sq%%%',"%%%dq%%%"];
                $translations = [];

                // serach for translations
                foreach ($files as $file) {
                    $_file = new File($file);
                    $contents = $_file->read(false,'r');
                    $_file->close();

                    // file
                    $file = str_replace(Configure::read('App.paths.plugins.0'), '', $file);

                    // prepare
                    $contents = str_replace($search, $replace, $contents);

                    // singular
                    foreach($combinations['singular'] as $combination){
                        if(preg_match_all("|__d\([ ]*" . $combination[0] . $i18n['domain'] . $combination[0] . ",[ ]*" . $combination[1] . "(.*)" . $combination[1] . "(.*)\)|U", $contents, $matches)){
                            foreach($matches[1] as $k => $match){
                                $match = str_replace($replace, $convert, $match);
                                $key = md5($match);
                                if(!array_key_exists($key,$translations)){
                                    $translations[$key] = [
                                        'key' => $match,
                                        'files' => [$file],
                                        'rel' => '',
                                    ];
                                }else if(!in_array($file, $translations[$key]['files'])){
                                    $translations[$key]['files'][] = $file;
                                }
                            }
                        }
                    }

                    // plural
                    foreach($combinations['plural'] as $combination){
                        if(preg_match_all("|__dn\([ ]*" . $combination[0] . $i18n['domain'] . $combination[0] . ",[ ]*" . $combination[1] . "(.*)" . $combination[1] . ",[ ]*" . $combination[2] . "(.*)" . $combination[2] . "(.*)\)|U", $contents, $matches)){
                            foreach($matches[1] as $k => $match){

                                // save singular
                                $match = str_replace($replace, $convert, $match);
                                $key = md5($match);
                                if(!array_key_exists($key,$translations)){
                                    $translations[$key] = [
                                        'key' => $match,
                                        'files' => [$file],
                                        'rel' => '',
                                    ];
                                }else if(!in_array($file, $translations[$key]['files'])){
                                    $translations[$key]['files'][] = $file;
                                }

                                // save plural
                                $rel = $key;
                                $match2 = str_replace($replace, $convert, $matches[2][$k]);
                                $key2 = md5($match2);
                                if(!array_key_exists($key2,$translations)){
                                    $translations[$key2] = [
                                        'key' => $match2,
                                        'files' => [$file],
                                        'rel' => $rel,
                                    ];
                                }else{
                                    if(!in_array($file, $translations[$key2]['files'])){
                                        $translations[$key2]['files'][] = $file;
                                    }
                                    $translations[$key2]['rel'] = $rel;
                                }
                            }
                        }
                    }
                }

                // keep
                $keep = [];

                // save new translations in database
                foreach(Configure::read($i18n['list']) as $locale => $settings){
                    if($settings['active']){
                        foreach($translations as $checksum => $info){

                            // save or update
                            $save = $this->Translations->query();
                            $check = $this->Translations->find()->where(['id' => $checksum, 'locale' => $locale, 'domain' => $i18n['domain']])->limit(1);
                            if($check->count() > 0){
                                $save->update()->set(['rel' => $info['rel'], 'files' => json_encode($info['files'])])->where(['id' => $checksum, 'locale' => $locale, 'domain' => $i18n['domain']])->execute();
                            }else{
                                $save->insert(['id', 'fallback', 'locale', 'domain', 'rel', 'translated', 'files', 'created', 'modified'])->values([
                                    'id' => $checksum,
                                    'fallback' => $info['key'],
                                    'locale' => $locale,
                                    'domain' => $i18n['domain'],
                                    'rel' => $info['rel'],
                                    'translated' => 0,
                                    'files' => json_encode($info['files']),
                                    'created' => $now,
                                    'modified' => $now,
                                ])->execute();
                            }

                            // keep
                            if(!in_array($checksum, $keep)){
                                $keep[] = $checksum;
                            }
                        }
                    }
                }

                // cleanup
                $cleanup = $this->Translations->query();
                if(count($keep) > 0){
                    $cleanup->delete()->where(['domain' => $i18n['domain']])->where(['id NOT IN' => $keep])->execute();
                }else{
                    $cleanup->delete()->where(['domain' => $i18n['domain']])->execute();
                }

            }
        }

        // success
        $this->Flash->success(__d('be', 'List of translations was updated!'));

        // redirect
        $this->redirect(['controller' => 'translations', 'action' => 'index']);
    }

    public function load(){

        // init
        $ret = ['success' => false, 'msg' => __d('be', 'Invalid request!')];
        $this->autoRender = false;

        // fetch data
        if($this->request->is('post')){
            $translation = $this->Translations->newEntity($this->request->data(), ['validate' => 'load']);
            if (!$translation->errors()) {
                $query = $this->Translations->find('all')->select(['id', 'locale', 'domain', 'fallback', 'translation'])->where(['id' => $this->request->data['id'], 'domain' => $this->request->data['domain'], 'locale' => $this->request->data['locale']]);
                if($query->count() > 0){
                    $ret['success'] = true;
                    $ret['msg'] = false;
                    $ret['data'] = $query->first();
                }else{
                    $ret['msg'] = __d('be', 'Translation not found!');
                }
            }else{
                $ret['msg'] = __d('be', 'Validation faild!');
                $ret['errors'] = $translation->errors();
            }
        }

        // result
        echo json_encode($ret);
        exit;
    }

    public function update(){

        // init
        $ret = ['success' => false, 'msg' => __d('be', 'Invalid request!')];
        $this->autoRender = false;

        if ($this->request->is('post')) {

            $translation = $this->Translations->newEntity($this->request->data(), ['validate' => 'update']);
            if (!$translation->errors() && $this->Translations->save($translation)) {
                $ret['success'] = true;
                $ret['msg'] = false;
                $ret['data'] = $this->request->data();
                $ret['translation'] = htmlspecialchars($this->request->data('translation'));
            }else{
                $ret['msg'] = __d('be', 'Validation faild!');
                $ret['errors'] = $translation->errors();
            }

        }

        // result
        echo json_encode($ret);
        exit;
    }

    public function import(){

        // init
        $check = false;
        $ret = ['success' => false, 'msg' => __d('be', 'Invalid request!'), 'failed' => []];
        $this->autoRender = false;
        $languages = Configure::read('languages');
        $translations = Configure::read('translations');

        // load all
        if($this->request->is('post') && array_key_exists('domain', $this->request->data) && strpos($this->request->data['domain'], ':') !== false && isset($_FILES) && is_array($_FILES) && array_key_exists('import', $_FILES)){
            list($domain, $locale) = explode(":", $this->request->data['domain']);
            foreach(Configure::read('plugin_i18n') as $d){
                if($check === false && $d['domain'] == $domain && $d['fetch'] && array_key_exists($locale, ${$d['list']}) && ${$d['list']}[$locale]['active']){
                    $check = true;
                }
            }

            // process
            if($check){
                if($_FILES['import']['error'] == 0){

                    $fallbacks = [];
                    $translations = [];
                    $map = [];

                    // handle
                    $handle = fopen($_FILES['import']['tmp_name'], "r");

                    if ($handle !== false) {

                        // read file
                        $line = 0;
                        while (($row = fgetcsv($handle, false, ";")) !== false) {
                            if (array_key_exists(0, $row) && array_key_exists(1, $row)) {
                                if(!empty($row[0]) && !empty($row[1])){
                                    $fallbacks[] = htmlspecialchars_decode(html_entity_decode(utf8_encode($row[0])));
                                    $translations[] = htmlspecialchars_decode(html_entity_decode(utf8_encode($row[1])));
                                }
                                $line++;
                            } else {
                                $ret['msg'] = __d('be', 'Invalid CSV file!');
                                break;
                            }
                        }
                        fclose($handle);

                        if($line > 0){
                            $existing = $this->connection->execute("SELECT `id`, `fallback` FROM `translations` WHERE `locale` = :locale AND `domain` = :domain ORDER BY `fallback`", ['locale' => $locale, 'domain' => $domain])->fetchAll('assoc');
                            if(is_array($existing) && count($existing) > 0){
                                foreach($existing as $translation){
                                    $map[htmlspecialchars_decode(html_entity_decode($translation['fallback']))] = $translation['id'];
                                }
                                foreach($fallbacks as $idx => $fallback){
                                    if(array_key_exists($fallback, $map)){
                                       $this->connection->execute("UPDATE `translations` SET `translation` = :translation, `translated` = :translated WHERE `id` = :id AND `locale` = :locale AND `domain` = :domain LIMIT 1", ['translation' => $translations[$idx], 'translated' => 1, 'id' => $map[$fallback], 'locale' => $locale, 'domain' => $domain]);
                                    }else{
                                       $ret['failed'][] = ['f' => $fallback, 't' => $translations[$idx]];
                                    }
                                }
                                $ret['success'] = true;
                            }else{
                                $ret['msg'] = __d('be', 'No translations found!');
                            }
                        }else{
                            $ret['msg'] = __d('be', 'Nothing to import!');
                        }
                    } else {
                        $ret['msg'] = __d('be', 'Import failed!');
                    }
                }else{
                    switch ($_FILES['import']['error']){
                        case 1:
                        case 2:
                            $ret['msg'] = __d('be', 'Maximum file size exceeded!');
                            break;
                        case 3:
                            $ret['msg'] = __d('be', 'File not completly uploaded!');
                            break;
                        case 4:
                            $ret['msg'] = __d('be', 'No file uploaded!');
                            break;
                        case 6:
                            $ret['msg'] = __d('be', 'No temp folder!');
                            break;
                        case 7:
                            $ret['msg'] = __d('be', 'Saving uploaded file failed!');
                            break;
                        case 8:
                            $ret['msg'] = __d('be', 'Upload stopped from an PHP extension!');
                            break;
                    }
                }
            }
        }

        // result
        echo json_encode($ret);
        exit;
    }

    public function export(){

        // init
        $check = false;
        $ret = ['success' => false, 'msg' => __d('be', 'Invalid request!')];
        $this->autoRender = false;
        $languages = Configure::read('languages');
        $translations = Configure::read('translations');

        // load all
        if($this->request->is('post') && array_key_exists('domain', $this->request->data) && strpos($this->request->data['domain'], ':') !== false){
            list($domain, $locale) = explode(":", $this->request->data['domain']);
            foreach(Configure::read('plugin_i18n') as $d){
                if($check === false && $d['domain'] == $domain && $d['fetch'] && array_key_exists($locale, ${$d['list']}) && ${$d['list']}[$locale]['active']){
                    $check = true;
                }
            }

            // process
            if($check){

                // fetch all translations
                $fields = ['fallback', 'translation'];
                $translations = $this->connection->execute("SELECT `" . join("`, `", $fields) . "` FROM `translations` WHERE `locale` = :locale AND `domain` = :domain ORDER BY `translation` ASC, `fallback` ASC", ['locale' => $locale, 'domain' => $domain])->fetchAll('assoc');

                if(is_array($translations) && count($translations) > 0){

                    // create csv
                    $delimiter = ';';
                    $filename = 'translations-' . $domain . '-' . $locale . '.csv';
                    $path = ROOT . DS . Configure::read('App.webroot') . DS . 'backend' . DS . 'export' . DS;
                    if(!file_exists($path)) mkdir($path, 0777, true);

                    // open file
                    $handle = fopen($path . $filename, 'w');

                    // headers
                    fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
                    fputcsv($handle, array_map('strtoupper', $fields), $delimiter);

                    // write data
                    foreach ($translations as $k1 => $translation){
                        foreach($translation as $key => $value){
                            $translation[$key] = htmlspecialchars_decode(html_entity_decode($value));
                        }
                        fputcsv($handle, $translation, $delimiter);
                    }

                    // close
                    fclose($handle);

                    // result
                    $ret['success'] = true;
                    $ret['url'] = DS . 'backend' . DS . 'export' . DS . $filename . '?c=' . time();

                }else{
                    $ret['msg'] = __d('be', 'No translations found!');
                }

            }


        }

        // result
        echo json_encode($ret);
        exit;
    }

}
