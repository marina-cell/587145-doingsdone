<h2 class="content__main-heading">Вход на сайт</h2>

<form class="form" action="auth.php" method="post">
    <div class="form__row">
        <?php $classname_email = (isset($errors['email'])) ? "form__input--error" : "";
        $value_email = isset($user) ? $user['email'] : ""; ?>

        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?=$classname_email;?>" type="text" name="email" id="email" value="<?=$value_email;?>" placeholder="Введите e-mail">

        <?php if (isset($errors['email'])): ?>
            <p class="form__message">
                <?=$errors['email'];?>
            </p>
        <?php elseif (isset($errors['email_format'])): ?>
            <p class="form__message">
                <?=$errors['email_format'];?>
            </p>
        <?php elseif (isset($errors['email_invalid'])): ?>
            <p class="form__message">
                <?=$errors['email_invalid'];?>
            </p>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <?php $classname_password = (isset($errors['password'])) ? "form__input--error" : ""; ?>

        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?=$classname_password;?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

        <?php if (isset($errors['password'])): ?>
            <p class="form__message">
                <?=$errors['password'];?>
            </p>
        <?php elseif (isset($errors['password_invalid'])): ?>
            <p class="form__message">
                <?=$errors['password_invalid'];?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (isset($errors)): ?>
        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
    <?php endif; ?>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Войти">
    </div>
</form>
