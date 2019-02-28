<h2 class="content__main-heading">Регистрация аккаунта</h2>

<form class="form" action="register.php" method="post">
    <div class="form__row">
        <?php $classname_email = (isset($errors['email'])
                                OR isset($errors['email_format'])
                                OR isset($errors['email_double'])) ? "form__input--error" : "";
        $value_email = isset($user['email']) ? $user['email'] : ""; ?>

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
        <?php elseif (isset($errors['email_double'])): ?>
            <p class="form__message">
                <?=$errors['email_double'];?>
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
        <?php endif; ?>
    </div>

    <div class="form__row">
        <?php $classname_name = (isset($errors['name'])) ? "form__input--error" : "";
        $value_name = isset($user['name']) ? $user['name'] : ""; ?>

        <label class="form__label" for="name">Имя <sup>*</sup></label>

        <input class="form__input <?=$classname_name;?>" type="text" name="name" id="name" value="<?=$value_name;?>" placeholder="Введите имя">

        <?php if (isset($errors['name'])): ?>
            <p class="form__message">
                <?=$errors['name'];?>
            </p>
        <?php endif; ?>
    </div>


    <div class="form__row form__row--controls">
        <?php if (isset($errors)): ?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>
        <input class="button" type="submit" name="" value="Зарегистрироваться">
    </div>
</form>
