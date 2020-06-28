<?php
# vim: set shiftwidth=2 ts=2 expandtab softtabstop=2 ft=php:
require_once 'inc/init.php';
require 'inc/header.php';
$data = $PDO->query( <<<sql
SELECT * FROM `type`;
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
            <th scope="col">
              <div class="btn-group" role="group">
                <a href="type.php?action=new" type="button" class="disabled btn btn-sm btn-outline-primary">Nouveau</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr>
            <th scope="row"><?= htmlentities($row['id']) ?></th>
            <td><?= htmlentities($row['name']) ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="carton.php?action=new&type=<?=htmlentities($row['id'])?>" type="button" class="btn btn-sm btn-outline-primary">Nouveau carton</a>
                <a href="type.php?type=<?=htmlentities($row['id'])?>" type="button" class="disabled btn btn-sm btn-outline-primary">Liste des cartons</a>
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

