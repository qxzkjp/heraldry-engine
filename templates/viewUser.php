<?php if($this->params['user.exists']): ?>
<table>
    <tbody>
    <tr>
        <td>User name:</td>
        <td><?=$this->params['user.name']?></td>
    </tr>
    <tr>
        <td>Status:</td>
        <td><?=$this->params['user.access']?></td>
    </tr>
    <tr>
        <td><b>Permissions</b></td>
        <td></td>
    </tr>
    <?php foreach ($this->params['user.permissions'] as $name): ?>
    <tr>
        <td><?=$name?></td>
        <td></td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>
<div>
    User not found!
</div>
<?php endif ?>
