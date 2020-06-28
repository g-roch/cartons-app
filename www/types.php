<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';
$data = $PDO->query( <<<sql
SELECT `type`.*, COUNT(*) as quantity
FROM `type`
  LEFT JOIN `carton` ON `carton`.`type` = `type`.`id`
GROUP BY `type`.`id`
sql
);
?>
<div class="row">
  <div class="col">
    <h2>Liste des types de cartons</h2>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nom</th>
            <th scope="col">Qte</th>
            <th scope="col">
              <div class="btn-group" role="group">
                <a href="type.php?action=new" type="button" class="btn btn-sm btn-outline-primary">Nouveau</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr>
            <th scope="row"><?= htmlentities($row['type.id']) ?></th>
            <td><?= htmlentities($row['type.name']) ?></td>
            <td><?= htmlentities($row['.quantity']) ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="carton.php?action=new&type=<?=htmlentities($row['type.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Nouveau carton</a>
                <a href="type.php?id=<?=htmlentities($row['type.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Liste des cartons</a>
              </div>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
require 'inc/footer.php';

