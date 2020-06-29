<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';

const ALLOWED_ACTION = ['view', 'new', 'save', 'edit', 'delete'];

$data['content.id'] = $_GET['id'] ?? null;
$data['content.carton'] = $_GET['carton'] ?? null;
$data['content.name'] = $_GET['name'] ?? null;
$data['content.description'] = $_GET['description'] ?? null;
$data['content.quantity'] = $_GET['quantity'] ?? null;
$data['content.unit'] = $_GET['unit'] ?? null;

$action = $_GET['action'] ?? (isset($data['content.id']) ? 'view' : 'new');
if(!in_array($action, ALLOWED_ACTION, true)) throw new Exception();

// Delete
if($action == 'delete') {
  $sql = <<<sql
DELETE FROM `content`
WHERE `content`.`id` = :id
sql;
  $statement = $PDO->prepare($sql);
  $statement->execute([
    ':id' => $data['content.id']
  ]);
  $action = 'none';
}
// Save
if($action == 'save') {
  $values = [
    ':carton' => $data['content.carton'],
    ':name' => $data['content.name'],
    ':description' => $data['content.description'] == '' ? null : $data['content.description'],
    ':quantity' => $data['content.quantity'] == '' ? null : $data['content.quantity'],
    ':unit' => $data['content.unit'] == '' ? null : $data['content.unit'],
  ];
  $sql = <<<sql
SET 
  `content`.`carton` = :carton,
  `content`.`name` = :name,
  `content`.`description` = :description,
  `content`.`quantity` = :quantity,
  `content`.`unit` = :unit
sql;
  if($data['content.id'] == 'new') {
    $action = 'none';
    $sql = <<<sql
INSERT INTO `content` $sql
sql;
  } else {
    $action = 'view';
    $sql = <<<sql
UPDATE `content` $sql
WHERE `content`.`id` = :id
sql;
    $values[':id'] = $data['content.id'];
  }
  $statement = $PDO->prepare($sql);
  $statement->execute($values);
  if($data['content.id'] == 'new') {
    header('Location: carton.php?id='.((int) $data['content.carton']));
  }
}

if($action != 'none') {

  $statement = $PDO->prepare( <<<sql
SELECT * 
FROM `content`
  LEFT JOIN `carton` ON `content`.`carton` = `carton`.`id`
WHERE `content`.`id` = :id
sql
);

  $dbdata = [];
  if($action != 'new' && isset($data['content.id'])) {
    if($statement->execute([
      ':id' => $data['content.id'],
    ])) {
      $dbdata = $statement->fetch();
    }
  } elseif(isset($data['content.carton'])) {
    $statement = $PDO->prepare( <<<sql
SELECT * 
FROM `carton`
WHERE `carton`.`id` = :id
sql
);
    if($statement->execute([
      ':id' => $data['content.carton'],
    ])) {
      $dbdata = $statement->fetch();
    }
  }

  $show = $dbdata;
  $show['content.id'] = $show['content.id'] ?? null;


  $roIfView = $action == 'view' ? 'readonly="readonly"' : '';

?>
<div class="row">
  <div class="col">
    <?php if(isset($show['content.id'])) : ?>
      <h2>Contenu <?= '#'.htmlentities($show['content.id']) ?></h2>
    <?php else: ?>
      <h2>Nouveau contenu</h2>
    <?php endif ?>
    <form>
      <input type="hidden" name="carton" value="<?= htmlentities($show['carton.id'] ?? '') ?>" />
      <div class="form-row">
        <div class="form-group col-2">
          <label for="frm-id">Numéro interne</label>
          <input type="text" class="form-control" id="frm-id" readonly="readonly" name="id" value="<?= htmlentities($show['content.id']??$action) ?>" />
        </div>
        <div class="form-group col-2">
          <label for="frm-carton-code">Code du carton</label>
          <input readonly="readonly" <?=$roIfView?> type="text" class="form-control" id="frm-carton-code" value="<?= htmlentities($show['carton.code'] ?? '') ?>" />
        </div>
        <div class="form-group col">
          <label for="frm-carton-description">Description du carton</label>
          <input readonly="readonly" <?=$roIfView?> type="text" class="form-control" id="frm-carton-description" value="<?= htmlentities($show['carton.description'] ?? '') ?>" />
        </div>
      </div>
      <div class="form-group">
        <label for="frm-name">Nom</label>
        <input required="required" <?=$roIfView?> type="text" class="form-control" id="frm-name" name="name" value="<?= htmlentities($show['content.name'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <label for="frm-description">Description</label>
        <textarea type="text" <?=$roIfView?> class="form-control" id="frm-description" name="description" ><?= htmlentities($show['content.description'] ?? '') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group col-2">
          <label for="frm-quantity">Quantité</label>
          <input <?=$roIfView?> type="number" min="0" class="form-control" id="frm-quantity" name="quantity" value="<?= htmlentities($show['content.quantity'] ?? '1') ?>" />
        </div>
        <div class="form-group col">
          <label for="frm-unit">Unité</label>
          <input <?=$roIfView?> type="text" class="form-control" id="frm-unit" name="unit" value="<?= htmlentities($show['content.unit'] ?? 'pce') ?>" />
        </div>
      </div>
      <div class="btn-group" role="group" aria-label="Basic example">
        <?php if($action == 'edit' || $action == 'new'): ?>
          <button type="submit" name=action value=save class="btn btn-outline-success">Enregistrer</button>
          <button type="reset" class="btn btn-outline-warning">Effacer le formulaire</button>
        <?php endif ?>
        <?php if(isset($show['content.carton'])): ?>
          <a href="carton.php?id=<?=htmlentities($show['content.carton'])?>" type="button" class="btn btn-outline-warning">Retour au carton</a>
        <?php endif ?>
        <button type="submit" name=action value=delete class="btn btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce contenu')" >Supprimer</button>
      </div>
    </form>
  </div>
</div>
<?php
} // if($action != 'none')
require 'inc/footer.php';



