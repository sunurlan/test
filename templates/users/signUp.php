<?php include __DIR__ . '/../header.php'; ?>
<div>
    <div class="users-signup-form-block">
        <h1>Регистрация</h1>
        <?php if (!empty($error)): ?>
            <div style="background-color: red; padding: 5px; margin: 15px; text-align:center"><?= $error ?></div>
        <?php endif; ?>
        <form action="/users/register" method="post">
        <div class="field">
            <div class="label">Nickname</div>
            <div class="input">
                <input type="text" name="nickname" value="<?= $_POST['nickname'] ?? '' ?>">
            </div>
        </div>
        <div class="field">
            <div class="label">Email</div>
            <div class="input">
                <input type="text" name="email" value="<?= $_POST['email'] ?? '' ?>">
            </div>
        </div>
        <div class="field">
            <div class="label">Пароль</div>
            <div class="input">
                <input type="password" name="password" value="<?= $_POST['password'] ?? '' ?>">
            </div>
        </div>
        <input class="submit" type="submit" value="Зарегистрироваться">
        </form>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>