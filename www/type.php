<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';

const ALLOWED_ACTION = ['view', 'new', 'save', 'edit'];

$data['type.id'] = $_GET['id'] ?? null;
$data['type.name'] = $_GET['name'] ?? null;

$action = $_GET['action'] ?? (isset($data['type.id']) ? 'view' : 'new');
if(!in_array($action, ALLOWED_ACTION, true)) throw new Exception();

// Save
if($action == 'save') {
  $values = [
    ':name' => $data['type.name'],
  ];
  $sql = <<<sql
SET 
  `type`.`name` = :name
sql;
  if($data['type.id'] == 'new') {
    $action = 'none';
    $sql = <<<sql
INSERT INTO `type` $sql
sql;
  } else {
    $action = 'view';
    $sql = <<<sql
UPDATE `type` $sql
WHERE `type`.`id` = :id
sql;
    $values[':id'] = $data['type.id'];
  }
  $statement = $PDO->prepare($sql);
  $statement->execute($values);
}

if($action != 'none') {

  $statement = $PDO->prepare( <<<sql
SELECT * 
FROM `type`
WHERE `type`.`id` = :id
sql
);

  $dbdata = [];
  if($action != 'new' && isset($data['type.id'])) {
    if($statement->execute([
      ':id' => $data['type.id'],
    ])) {
      $dbdata = $statement->fetch();
    }
  }

  $show = $dbdata;
  $show['type.id'] = $show['type.id'] ?? null;
  $show['type.name'] = $show['type.name'] ?? null;

  $roIfView = $action == 'view' ? 'readonly="readonly"' : '';
?>
<div class="row">
  <div class="col">
    <?php if(isset($show['type.id'])) : ?>
      <h2>Type <?= '#'.htmlentities($show['type.id']) ?></h2>
    <?php else: ?>
      <h2>Nouveau type</h2>
    <?php endif ?>
    <form>
      <div class="form-group">
        <label for="frm-id">Numéro interne</label>
        <input type="text" class="form-control" id="frm-id" readonly="readonly" name="id" value="<?= htmlentities($show['type.id']??$action) ?>" />
      </div>
      <div class="form-group">
        <label for="frm-name">name</label>
        <input required="required" <?=$roIfView?> type="text" class="form-control" id="frm-name" name="name" value="<?= htmlentities($show['type.name'] ?? '') ?>" />
      </div>
      <div class="btn-group" role="group" aria-label="Basic example">
        <?php if($action == 'edit'): ?>
          <button type="submit" name=action value=save class="btn btn-outline-success">Enregistrer</button>
          <button type="reset" class="btn btn-outline-warning">Effacer le formulaire</button>
        <?php endif ?>
        <a href="types.php" type="button" class="btn btn-outline-warning">Retour à la liste des types</a>
      </div>
    </form>
  </div>
</div>
<?php if($action == 'view'): ?>
<?php
$data = $PDO->prepare( <<<sql
SELECT *
FROM `carton`
WHERE `carton`.`type` = :type
sql
);
$data->execute([
  ':type' => $show['type.id'],
]);

?>
<hr />
<div class="row">
  <div class="col">
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Code</th>
            <th scope="col">Description</th>
            <th scope="col">
              <div class="btn-group" role="group">
                <a href="carton.php?action=new&type=<?=htmlentities($show['type.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Nouveau</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr>
            <th scope="row"><?= htmlentities($row['carton.id']) ?></th>
            <td><?= htmlentities($row['carton.code']) ?></td>
            <td><?= htmlentities($row['carton.description']) ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="#" type="button" class="disabled btn btn-sm btn-outline-primary">Ajouter du contenu</a>
                <a href="carton.php?action=view&id=<?=htmlentities($row['carton.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Voir le contenu</a>
                <a href="carton.php?action=edit&id=<?=htmlentities($row['carton.id'])?>" type="button" class="btn btn-sm btn-outline-warning">Éditer le carton</a>
              </div>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif /* $action == 'view' */ ?>
<?php
} // if($action != 'none')
require 'inc/footer.php';



