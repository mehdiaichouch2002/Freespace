<?php
include 'partials/header.php';

if(isset($_GET['id'])) {
    $id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection,$query);
    $user=mysqli_fetch_assoc($result);
} else {
    header('location: '.ROOT_URL.'admin/manage-users.php');
    die();
}
?>
<section class="form__section">
    <div class="container form__section-container add_user-container">
        <h2>Edit User</h2>
        <form action="<?= ROOT_URL ?>admin/edit-user-logic.php" method="post">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <input type="text" name="firstname" value="<?= $user['firstname'] ?>" placeholder="First name">
            <input type="text" name="lastname" value="<?= $user['lastname'] ?>" placeholder="Last name">
            <input type="text" name="username" value="<?= $user['username'] ?>" placeholder="Username">
            <select name="userrole">
                <option <?=$user['is_admin']?"":"selected"?> value="0">Author</option>
                <option value="1" <?=$user['is_admin']?"selected":""?> >Admin</option>
            </select>
            <button type="submit" name="submit" class="btn">Update User</button>
        </form>
    </div>
</section>
<!--=================== END OF edit  User ==================-->

<?php
include '../partials/footer.php'
?>