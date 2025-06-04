<h1><?= __d('be', 'Login'); ?></h1>
<div class="users form">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create() ?>
    <?= $this->Form->input('username', ['label' => false, 'placeholder' => __d('be', 'Username')]) ?>
    <?= $this->Form->input('password', ['label' => false, 'placeholder' => __d('be', 'Password')]) ?>
    <?= $this->Form->button(__d('be', 'Login'), ['class' => 'submit']); ?>
    <?= $this->Form->end() ?>
</div>