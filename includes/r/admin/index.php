<?php
AdminOnly();
SetTitle('Админ-панель');
GetHeader();
?>
<div class="list-group">
<li><a href="/?r=admin/add_domain" title="Добавить новый домен" class="list-group-item">Добавить домен</a></li>
<li><a href="/?r=admin/domains" title="Все домены" class="list-group-item">Все домены</a></li>
<li><a href="/?r=admin/users" title="Все пользователи" class="list-group-item">Пользователи</a></li>
</div>
<?php
GetFooter();