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
    $action = 'show';
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
      <?php if($action == 'edit'): ?>
        <div class="btn-group" role="group" aria-label="Basic example">
          <button type="submit" name=action value=save class="btn btn-outline-success">Enregistrer</button>
          <button type="reset" class="btn btn-outline-warning">Effacer le formulaire</button>
        </div>
      <?php endif ?>
    </form>
  </div>
</div>
<?php
} // if($action != 'none')
require 'inc/footer.php';



