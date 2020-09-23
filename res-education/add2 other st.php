<?php
// Start a session
session_start();

// PDO database connection
require_once 'pdo.php';

// Utility (helper) functions
require_once 'util.php';

// Check if the user is logged in
check_auth();

// If the user requested cancellation, redirect to index.php
if ( isset($_POST['cancel']) ) {
    return redirect('index.php');
}

if ( isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary']) ) {
    // Validate profile entries
    if ( ! validate_profile() ) {
        return redirect('add.php');
    }

    // Validate education entries
    if (! validate_education() ) {
        return redirect('add.php');
    }

    // Validate position entries
    if (! validate_position() ) {
        return redirect('add.php');
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO `profile` (`user_id`, `first_name`, `last_name`, `email`, `headline`, `summary`) VALUES ( :uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
        ]);
        $profile_id = $pdo->lastInsertId();

        // Insert the education entries
        insert_educations($pdo, $profile_id);

        // Insert the position entries
        insert_positions($pdo, $profile_id);
        
        $_SESSION['success'] = 'Profile added.';
        return redirect('index.php');
    } catch (PDOException $e) {
        error_log('Query failed: '.$e->getMessage());
        $_SESSION['error'] = 'Unexpected error occured.';
        return redirect('add.php');
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Novruzgeldi Bayramberdiyev's Profile Add</title>
        <?php require_once 'styles_scripts.php'; ?>
    </head>
    <body>
        <div class="container">
            <h1 class="mb-4">Adding Profile for <?= $_SESSION['name'] ?></h1>
            <?php flash_messages(); ?>
            <form method="post">
                <div class="form-group row">
                    <label for="first_name" class="col-md-2 col-form-label">First Name</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="first_name" id="first_name" size="60">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="last_name" class="col-md-2 col-form-label">Last Name</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="last_name" id="last_name" size="60">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-md-2 col-form-label">Email</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="email" id="email" size="30">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="headline" class="col-md-2 col-form-label">Headline</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="headline" id="headline" size="80">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="summary" class="col-md-2 col-form-label">Summary</label>
                    <div class="col-md-10">
                        <textarea cols="80" rows="8" class="form-control" name="summary" id="summary"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="add_education" class="col-md-2 col-form-label">Education</label>
                    <div class="col-md-10">
                        <input type="submit" class="btn btn-success" id="add_education" value="+">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="education_fields" class="col-md-2 col-form-label"></label>
                    <div class="col-md-10" id="education_fields"></div>
                </div>
                <div class="form-group row">
                    <label for="add_position" class="col-md-2 col-form-label">Position</label>
                    <div class="col-md-10">
                        <input type="submit" class="btn btn-success" id="add_position" value="+">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="position_fields" class="col-md-2 col-form-label"></label>
                    <div class="col-md-10" id="position_fields"></div>
                </div>
                <div class="form-group row">
                    <div class="col-12">
                        <input type="submit" class="btn btn-primary" value="Add">
                        <input type="submit" name="cancel" class="btn btn-secondary" value="Cancel">
                    </div>
                </div>
            </form>
        </div>
        <script>
            let countEdu = 0;
            let countPos = 0;

            $(document).ready(function () {
                $('#add_education').click(function (event) {
                    event.preventDefault();
                    if ( countEdu >= 9 ) {
                        alert('Maximum of nine education entries exceeded.');
                        return;
                    }
                    countEdu++;
                    $('#education_fields').append(`
                        <div id="education${countEdu}">
                            <div class="form-group row">
                                <label for="edu_year${countEdu}" class="col-md-2 col-form-label">Year:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="edu_year${countEdu}" />
                                </div>
                                <div class="col-md-2">
                                    <input type="button" class="btn btn-danger" onclick="$('#education${countEdu}').remove();return false;" value="-">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="school${countEdu}" class="col-md-2 col-form-label">School:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control school" name="school${countEdu}" size="80" />
                                </div>
                            </div>
                        </div>
                    `);

                    $('.school').autocomplete({
                        source: 'school.php'
                    });
                });

                $('#add_position').click(function (event) {
                    event.preventDefault();
                    if ( countPos >= 9 ) {
                        alert('Maximum of nine position entries exceeded.');
                        return;
                    }
                    countPos++;
                    $('#position_fields').append(`
                        <div id="position${countPos}">
                            <div class="form-group row">
                                <label for="pos_year${countPos}" class="col-md-2 col-form-label">Year:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="pos_year${countPos}" />
                                </div>
                                <div class="col-md-2">
                                    <input type="button" class="btn btn-danger" onclick="$('#position${countPos}').remove();return false;" value="-">
                                </div>
                            </div>
                            <div class="form-group row justify-content-md-center">
                                <div class="col-md-8">
                                    <textarea name="desc${countPos}" class="form-control" rows="8" cols="80"></textarea>
                                </div>
                            </div>
                        </div>
                    `);
                });
            });
        </script>
    </body>
</html>