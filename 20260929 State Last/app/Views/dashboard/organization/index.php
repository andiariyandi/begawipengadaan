<h3>Master Organization / OPD</h3>

<table>
<thead>
<tr>
<th>Kode</th>
<th>Nama</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php foreach($organizations as $item): ?>
<tr>
<td><?= esc($item['code']) ?></td>
<td><?= esc($item['name']) ?></td>
<td><?= esc($item['status']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
