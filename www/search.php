<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:

$ajax = ($_GET['ajax'] ?? 'false') == 'true' ? true : false;

require_once 'inc/init.php';
if(!$ajax) require 'inc/header.php';

if(isset($_GET['q'])) {

  $q = "%$_GET[q]%";

  $data = $PDO->prepare( <<<sql
  SELECT `carton`.*, `content`.*
  FROM `content`
  LEFT JOIN `carton` ON `content`.`carton` = `carton`.`id`
  WHERE 
    `content`.`name` LIKE :search
    OR `content`.`description` LIKE :search
  ORDER BY `carton`.`code` ASC, `content`.`name` ASC
sql
  );
  $data->execute([
    ':search' => $q
  ]);

  $data = $data->fetchAll(PDO::FETCH_GROUP);
} else {
  $data = [];
}

?>

<?php if(!$ajax): ?>
<div class="row">
  <div class="col">
    <div class="form-group">
      <label for="search-q">Rechercher</label>
      <input id="search-q" class="form-control" value="<?= $_GET['q'] ?? '' ?>" type="text" oninput="$.ajax({data: {ajax:true, q:this.value}}).done(function(data) {$('#ajax-dest').html(data);})"/>
    </div>
  </div>
</div>
<div class="row">
  <div class="col" id="ajax-dest">
<?php endif ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-sm">
        <thead>
          <tr>
            <th class="table-secondary" scope="col">#</th>
            <th class="table-secondary" scope="col">Carton</th>
            <th class="table-secondary" colspan="2" scope="col">Description du carton</th>
            <th class="table-secondary" scope="col"></th>
          </tr>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nom</th>
            <th scope="col">Description</th>
            <th scope="col">Qte</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $group): ?>
          <tr class="table-secondary">
            <th scope="row"><?= htmlentities($group[0]['content.carton']) ?></th>
            <th><?= htmlentities($group[0]['carton.code']) ?></th>
            <th colspan="2"><?= htmlentities($group[0]['carton.description']) ?></th>
            <td>
              <div class="btn-group" role="group">
	      	      <a href="content.php?carton=<?=htmlentities($group[0]['content.carton'])?>" type="button" class="btn btn-sm btn-outline-primary">Ajouter du contenu</a>
                <a href="carton.php?action=view&id=<?=htmlentities($group[0]['content.carton'])?>" type="button" class="btn btn-sm btn-outline-primary">Voir</a>
                <a href="carton.php?action=edit&id=<?=htmlentities($group[0]['content.carton'])?>" type="button" class="btn btn-sm btn-outline-warning">Éditer</a>
              </div>
            </td>
          </tr>
          <?php foreach($group as $row): ?>
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
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
<?php if(!$ajax): ?>
  </div>
</div>
<?php endif ?>

<?php
if(!$ajax) require 'inc/footer.php';
