<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';


$data = $PDO->prepare( <<<sql
SELECT *
FROM `tag`
ORDER BY `tag`.`name` ASC
sql
);
$data->execute([
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
            <th scope="col">Couleur</th>
            <th scope="col">Priorité</th>
            <th scope="col">
              <div class="btn-group" role="group">
                <a href="#" type="button" class="disabled btn btn-sm btn-outline-primary">Nouveau</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr class="table-<?= htmlentities($row['tag.color']) ?>">
            <th scope="row"><?= htmlentities($row['tag.id']) ?></th>
            <td><?= htmlentities($row['tag.name']) ?></td>
            <td><?= htmlentities($row['tag.color']) ?></td>
            <td><?= htmlentities($row['tag.color_prio']) ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="#" type="button" class="disabled btn btn-sm btn-primary">Voir l'objet</a>
                <a href="#" type="button" class="disabled btn btn-sm btn-warning">Éditer l'objet</a>
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


