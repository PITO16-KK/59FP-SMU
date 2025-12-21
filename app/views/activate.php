<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - Sistem Monitoring Udara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #37508e, #c9d6ff);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            background-color: #fff;
            animation: fadeIn 1s ease-in-out;
        }

        .card h2 {
            font-weight: 700;
            margin-bottom: 20px;
            color: #324987;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .btn-success {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #27406b;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 15px;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="card">
    <i class="bi bi-check-circle-fill" style="font-size: 60px; color: #28a745;"></i>
    <h2>Aktivasi Akun</h2>

    <?php if(isset($message)): ?>
        <div class="alert alert-info mt-3">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php else: ?>
        <p class="mt-3">Memproses aktivasi akun...</p>
    <?php endif; ?>

    <a href="/sistem_monitoring_udara/public/login" class="btn btn-success mt-4">Login</a>
</div>

</body>
</html>
