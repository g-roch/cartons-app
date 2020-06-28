<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';

const ALLOWED_ACTION = ['view', 'new', 'save', 'edit'];

$data['carton.id'] = $_GET['id'] ?? null;
$data['carton.type'] = $_GET['type'] ?? null;
$data['carton.code'] = $_GET['code'] ?? null;
$data['carton.description'] = $_GET['description'] ?? null;

$action = $_GET['action'] ?? (isset($data['carton.id']) ? 'view' : 'new');
if(!in_array($action, ALLOWED_ACTION, true)) throw new Exception();

// Save
if($action == 'save') {
  $values = [
    ':type' => $data['carton.type'],
    ':code' => $data['carton.code'],
    ':description' => $data['carton.description'],
  ];
  $sql = <<<sql
SET 
  `carton`.`type` = :type,
  `carton`.`code` = :code,
  `carton`.`description` = :description
sql;
  if($data['carton.id'] == 'new') {
    $action = 'none';
    $sql = <<<sql
INSERT INTO `carton` $sql
sql;
  } else {
    $action = 'show';
    $sql = <<<sql
UPDATE `carton` $sql
WHERE `carton`.`id` = :id
sql;
    $values[':id'] = $data['carton.id'];
  }
  $statement = $PDO->prepare($sql);
  $statement->execute($values);
}

if($action != 'none') {

  $statement = $PDO->prepare( <<<sql
SELECT * 
FROM `carton`
  LEFT JOIN `type` ON `carton`.`type` = `type`.`id`
WHERE `carton`.`id` = :id
sql
);

  $dbdata = [];
  if($action != 'new' && isset($data['carton.id'])) {
    if($statement->execute([
      ':id' => $data['carton.id'],
    ])) {
      $dbdata = $statement->fetch();
    }
  }

  $show = $dbdata;
  $show['carton.id'] = $show['carton.id'] ?? null;
  $show['carton.type'] = $show['carton.type'] ?? $_GET['type'] ?? null;


  $types = $PDO->query(<<<sql
SELECT *
FROM `type`
sql
)->fetchAll();

  $roIfView = $action == 'view' ? 'disabled="disabled"' : '';

?>
<div class="row">
  <div class="col">
    <?php if(isset($show['carton.id'])) : ?>
      <h2>Carton <?= '#'.htmlentities($show['carton.id']) ?></h2>
    <?php else: ?>
      <h2>Nouveau carton</h2>
    <?php endif ?>
    <form>
      <div class="form-group">
        <label for="frm-id">Numéro interne</label>
        <input type="text" class="form-control" id="frm-id" readonly="readonly" name="id" value="<?= htmlentities($show['carton.id']??$action) ?>" />
      </div>
      <div class="form-group">
        <label for="frm-type">Type</label>
        <select required="required" <?=$roIfView?> class="form-control custom-select" id="frm-type" name="type">
          <option <?= isset($show['carton.id']) ? '' : 'selected="selected" ' ?> disabled="disabled">Veuillez selectioner</option>
          <?php foreach($types as $type): ?>
          <option <?= $type['type.id'] == $show['carton.type'] ? 'selected="selected" ' : '' ?> value="<?= $type['type.id'] ?>" ><?= $type['type.id'] ?> - <?= $type['type.name'] ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label for="frm-code">Code</label>
        <input required="required" <?=$roIfView?> type="text" class="form-control" id="frm-code" name="code" value="<?= htmlentities($show['carton.code'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <label for="frm-description">Description</label>
        <textarea type="text" <?=$roIfView?> class="form-control" id="frm-description" name="description" ><?= htmlentities($show['carton.description'] ?? '') ?></textarea>
      </div>
      <div class="btn-group" role="group" aria-label="Basic example">
        <?php if($action == 'edit' || $action == 'new'): ?>
          <button type="submit" name=action value=save class="btn btn-outline-success">Enregistrer</button>
          <button type="reset" class="btn btn-outline-warning">Effacer le formulaire</button>
        <?php endif ?>
        <?php if(isset($show['carton.type'])): ?>
          <a href="type.php?id=<?=htmlentities($show['carton.type'])?>" type="button" class="btn btn-outline-warning">Retour au type</a>
        <?php endif ?>
      </div>
    </form>
  </div>
</div>
<?php if($action == 'view'): ?>
<?php
$data = $PDO->prepare( <<<sql
SELECT *
FROM `content`
WHERE `content`.`carton` = :carton
sql
);
$data->execute([
  ':carton' => $show['carton.id'],
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
            <th scope="col">Nom</th>
            <th scope="col">Description</th>
            <th scope="col">Qte</th>
            <th scope="col">
              <div class="btn-group" role="group">
                <a href="content.php?action=new&carton=<?=htmlentities($show['carton.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Nouveau</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr>
            <th scope="row"><?= htmlentities($row['content.id']) ?></th>
            <td><?= htmlentities($row['content.name']) ?></td>
            <td><?= htmlentities($row['content.description']) ?></td>
            <td>
              <?= htmlentities($row['content.quantity'] ?? '') ?>
              <?= htmlentities($row['content.unit'] ?? '') ?>
            </td>
            <td>
              <div class="btn-group" role="group">
                <a href="content.php?action=view&id=<?=htmlentities($row['content.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Voir l'objet</a>
                <a href="content.php?action=edit&id=<?=htmlentities($row['content.id'])?>" type="button" class="btn btn-sm btn-outline-warning">Éditer l'objet</a>
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


