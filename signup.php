<?php

$dsn = 'mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$errors = [];
$welcomeName = null;
$welcomeCity = null;
// values  to keep in the form after failed submit
$name='';
$age='';
$city='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name=trim($_POST['name'] ?? '');
    $age=trim($_POST['age'] ?? '');
    $city=trim($_POST['city'] ?? '');
  
    // TODO 1: Validate name and city. Put messages in $errors.
    if($name===''){
      $errors[]='Name is required';
    }
    if ($city === '') {
        $errors[] = 'City is required';
    }
    if ($age !== '' && (!ctype_digit($age) || (int)$age <= 0)) {
        $errors[] = 'Age must be a positive whole number';
    }


    // TODO 2: If $errors is empty, INSERT the user with a prepared statement.
    if(empty($errors)){
      $stmt=$pdo->prepare(
        'INSERT INTO users(name,age,city)VALUES(:name,:age,:city)'
      );

      $stmt->execute([
        'name'=>$name,
        'age'=>$age==='' ? null : $age,
        'city'=>$city,
      ]);
      $welcomeName=$name;
      $welcomeCity=$city;

      $name = $age = $city = '';

    }
}
$users = $pdo->query('SELECT name, age, city FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: #f0faf4;
            font-family: 'Segoe UI', sans-serif;
            color: #1a3a2a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 16px;
        }

        /* ── Card ── */
        .card {
            background: #ffffff;
            border: 1px solid #b6e5c8;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(34, 139, 80, 0.12);
            padding: 36px 40px;
            width: 100%;
            max-width: 420px;
        }

        h1 {
            text-align: center;
            font-size: 1.8rem;
            color: #1e7a48;
            margin-bottom: 24px;
        }

        /* ── Error Messages ── */
        .error-box {
            background: #fff3f3;
            border-left: 4px solid #e53935;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            color: #c62828;
            font-size: 0.9rem;
        }

        /* ── Form Inputs ── */
        .form-group { margin-bottom: 16px; }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #b6e5c8;
            border-radius: 8px;
            font-size: 1rem;
            color: #1a3a2a;
            background: #f7fdf9;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.15);
            background: #ffffff;
        }

        .form-group input::placeholder { color: #8cba9f; }

        /* ── Button ── */
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #27ae60, #1e8449);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
        }

        button[type="submit"]:hover  { opacity: 0.92; }
        button[type="submit"]:active { transform: scale(0.98); }

        /* ── Success Banner ── */
        .success-banner {
            background: #e8f8ef;
            border: 1px solid #81c995;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            text-align: center;
            color: #1e7a48;
            font-weight: 600;
        }

        /* ── Users Table ── */
        .users-section {
            width: 100%;
            max-width: 560px;
            margin-top: 40px;
        }

        h2 {
            font-size: 1.25rem;
            color: #1e7a48;
            margin-bottom: 14px;
            border-bottom: 2px solid #b6e5c8;
            padding-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(34, 139, 80, 0.08);
        }

        table th {
            background: #27ae60;
            color: #ffffff;
            padding: 11px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        table td {
            padding: 10px 16px;
            border-bottom: 1px solid #e8f5ee;
            font-size: 0.95rem;
        }

        table tr:last-child td { border-bottom: none; }
        table tr:hover td { background: #f0faf4; }

        .no-users {
            color: #6a9e7f;
            font-style: italic;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>Sign Up</h1>

        <!-- TODO 3: Show the errors here. -->
        <?php foreach ($errors as $error): ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>

        <form method="POST">
            <div class="form-group">
                <input name="name" placeholder="Name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <input name="age" placeholder="Age" value="<?= htmlspecialchars($age, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <input name="city" placeholder="City" value="<?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit">Sign Up</button>
        </form>

        <!-- TODO 4: Show "Welcome ___ from ___!" after a successful sign up. -->
        <?php if ($welcomeName !== null): ?>
            <div class="success-banner">
                Welcome <?= htmlspecialchars($welcomeName, ENT_QUOTES, 'UTF-8') ?>
                from <?= htmlspecialchars($welcomeCity, ENT_QUOTES, 'UTF-8') ?>!
            </div>
        <?php endif; ?>
    </div>

    <div class="users-section">
        <h2>All Users</h2>

        <?php if (empty($users)): ?>
            <p class="no-users">No users yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Name</th><th>Age</th><th>City</th></tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($user['age'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['city'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>
