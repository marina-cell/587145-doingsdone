<h2 class="content__main-heading">Добавление задачи</h2>

<form class="form"  action="add.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
        <?php $classname_require = isset($errors['name']) ? "form__input--error" : "";
        $value = isset($new_task['name']) ? $new_task['name'] : ""; ?>

        <label class="form__label" for="name">Название <sup>*</sup></label>

        <input class="form__input <?=$classname_require;?>" type="text" name="name" id="name" value="<?=$value;?>" placeholder="Введите название">

        <?php if (isset($errors['name'])): ?>
            <p class="form__message">
                <?=$errors['name'];?>
            </p>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <?php $classname_project = isset($errors['project']) ? "form__input--error" : ""; ?>

        <label class="form__label" for="project">Проект</label>

        <select class="form__input form__input--select <?=$classname_project;?>" name="project" id="project">
            <option value="">Входящие</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?=htmlspecialchars($project['id']);?>"><?=htmlspecialchars($project['name']);?></option>
            <?php endforeach; ?>
        </select>

        <?php if (isset($errors['project'])): ?>
            <p class="form__message">
                <?=$errors['project'];?>
            </p>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <?php $classname_date = isset($errors['date']) ? "form__input--error" : ""; ?>

        <label class="form__label" for="date">Дата выполнения</label>

        <input class="form__input form__input--date <?=$classname_date;?>" type="date" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
        <?php if (isset($errors['date'])): ?>
            <p class="form__message">
                <?=$errors['date'];?>
            </p>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <label class="form__label" for="preview">Файл</label>

        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="preview" id="preview" value="">

            <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
            </label>
        </div>
    </div>

    <?php if (isset($errors)): ?>
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>

    <?php endif; ?>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>
