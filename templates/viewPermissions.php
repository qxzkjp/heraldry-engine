<?php $acc=1; ?>
<table>
    <thead>
    <tr>
        <td>Permission ID</td>
        <td>Permission name</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($this->params['permission_names'] as $name): ?>
    <tr>
        <td><?= $acc ?></td>
        <td><?= $name ?></td>
    </tr>
    <?php
    ++$acc;
    endforeach ?>
    <?php if($acc===1):?>
    <td><i>no permissions found</i></td>
    <td></td>
    <?php endif ?>
    </tbody>
</table>