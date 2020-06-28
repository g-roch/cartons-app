<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';

$data = $PDO->prepare( <<<sql
SELECT *
FROM `carton`
sql
);
$data->execute();

?>
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
	      	<a href="content.php?carton=<?=htmlentities($row['carton.id'])?>" type="button" class="btn btn-sm btn-outline-primary">Ajouter du contenu</a>
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
<?php
require 'inc/footer.php';
