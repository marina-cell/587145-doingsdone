<h2 class="content__main-heading">Добавление проекта</h2>

<form class="form"  action="project.php" method="post">
    <div class="form__row">
        <?php $classname = isset($errors['name']) ? "form__input--error" : "";
        $value = isset($new_project['name']) ? $new_project['name'] : ""; ?>

        <label class="form__label" for="project_name">Название <sup>*</sup></label>

        <input class="form__input <?=$classname; ?>" type="text" name="name" id="project_name" value="<?=$value; ?>" placeholder="Введите название проекта">

        <?php if (isset($errors['name'])): ?>
            <p class="form__message">
                <?=$errors['name'];?>
            </p>
        <?php elseif (isset($errors['project'])): ?>
            <p class="form__message">
                <?=$errors['project'];?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (isset($errors)): ?>
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
    <?php endif; ?>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>
