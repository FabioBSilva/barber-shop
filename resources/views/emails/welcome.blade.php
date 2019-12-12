<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <title>Agora você é um cliente Wizzer!</title>
</head>

<style>
    * {
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        justify-content: center;
        font-family: 'Roboto', sans-serif;
        background-color: #EEEEEE;
        color: #27292E;
    }

    a {
        color: #2693FF;
        font-weight: bold;
    }

    .container {
        background-color: #f9f9f9;
        width: 500px;
    }

    .container>.logo,
    .title,
    .body {
        min-height: 50px;
    }

    .container>.logo,
    .title,
    .body,
    .footer {
        padding: 0 40px;
    }

    .container>.logo {
        padding-top: 30px;
        padding-bottom: 30px;
        text-align: center;
    }

    .container>.title {
        background-size: cover;
        background-position: bottom;
        background-repeat: no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-weight: 100;
        font-size: 32px;
        text-shadow: 1px 2px 0 rgba(0, 0, 0, 0.2);
        min-height: 120px;
    }

    .container>.body {
        display: flex;
        align-items: center;
        flex-direction: column;
        text-align: center;
    }

    .container>.body>p {
        margin-top: 45px;
    }

    .container>.footer {
        display: flex;
        align-items: center;
        flex-direction: column;
        margin-top: 120px;
        padding-bottom: 20px;
    }
    .container>.footer>p {
        color: #c3c3c3;
        font-size: 8pt;
    }
</style>

<body>
    <div class="container">

        <div class="logo">

            <img src="{{asset('images/logo.png')}}" width="150" alt="wizzer" srcset="">

        </div>
        <div style="background-image: url({{ asset('images/mail-background-blue.png') }});" class="title">

            <span>Olá {{$user->name}}</span>

        </div>

        <div class="body">

            "<p>Seja bem vindo <b>{{$user->name}}</b>! Clique aqui <a href="{{$token}}"></a> para verificar seu email</p>"

            <p>Agora você está a um passo de anunciar conosco.</p>

            <p>Temos uma coleção de planos esperando por você! <br>Se não encontrar um que melhor te atenda, sinta-se à vontade de solicitar um personalizado, <br><a href="#">clique aqui</a> e confira!</p>

        </div>

        <div class="footer">
            <p>Wizzer &#9400;</p>
            <p>Todos os direitos reservados</p>
        </div>

    </div>
</body>

</html>