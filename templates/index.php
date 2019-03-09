<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="?<?= http_build_query(array_merge($_GET, ['filter' => 'all'])); ?>" class="tasks-switch__item <?php if ($filter === 'all' || empty($filter)): ?>tasks-switch__item--active<?php endif; ?>">Все задачи</a>
        <a href="?<?= http_build_query(array_merge($_GET, ['filter' => 'agenda'])); ?>" class="tasks-switch__item <?php if ($filter === 'agenda'): ?>tasks-switch__item--active<?php endif; ?>">Повестка дня</a>
        <a href="?<?= http_build_query(array_merge($_GET, ['filter' => 'tomorrow'])); ?>" class="tasks-switch__item <?php if ($filter === 'tomorrow'): ?>tasks-switch__item--active<?php endif; ?>">Завтра</a>
        <a href="?<?= http_build_query(array_merge($_GET, ['filter' => 'overdue'])); ?>" class="tasks-switch__item <?php if ($filter === 'overdue'): ?>tasks-switch__item--active<?php endif; ?>">Просроченные</a>
    </nav>
    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($show_complete_tasks): ?>checked<?php endif; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($task_list as $key => $item): ?>
        <?php if($item['state'] and !$show_complete_tasks) { continue; } ?>
        <tr class="tasks__item task <?php if ($item['state']): ?>task--completed<?php endif; ?>
                                    <?php if (is_date_important($item['deadline'])): ?>task--important<?php endif; ?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?=$item['id']; ?>" <?php if ($item['state']): ?>checked<?php endif; ?>>
                    <span class="checkbox__text"><?=htmlspecialchars($item['task_name']); ?></span>
                </label>
            </td>

            <td class="task__file">
                <?php if (!empty($item['file'])): ?>
                    <a class="download-link" href="./uploads/<?=$item['file']; ?>"><?=$item['file']; ?></a>
                <?php endif; ?>
            </td>

            <td class="task__date"><?php if (isset($item['deadline'])) {print(date("d.m.Y", strtotime($item['deadline'])));} ?></td>
        </tr>
    <?php endforeach; ?>
</table>
