<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" async></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" async></script>

    <title>Buchung erfassen</title>
</head>
<body>
    <form>
        <div class="form-group">
            <label for="datum">Buchunsdatum</label>
            <input class="form-control" type="date" id="datum" name="datum" value="2018-09-09">
        </div>
        <div class="form-group">
            <label for="empfänger">Empfänger</label>
            <select class="form-control" id="empfänger" name="empfänger">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reNummer">Rechnungsnummer</label>
            <input class="form-control" type="text" id="reNummer" name="reNummer">
        </div>
        <div class="form-group">
            <label for="buchungstext">Beschreibung</label>
            <input class="form-control" type="text" id="buchungstext" name="buchungstext">
        </div>
        <div class="form-group">
            <label for="totalbetrag">Betrag</label>
            <input class="form-control" type="number" id="totalbetrag" name="totalbetrag">
        </div>
        <div class="form-group">
            <label for="kontoSoll">Konto Soll</label>
            <select class="form-control" id="kontoSoll" name="kontoSoll">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="kontoHaben">Konto Haben</label>
            <select class="form-control" id="kontoHaben" name="kontoHaben">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="periode">Periode</label>
            <select class="form-control" id="periode" name="periode">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation1">Klassifikation 1</label>
            <select class="form-control" id="klassifikation1" name="klassifikation1">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation2">Klassifikation 2</label>
            <select class="form-control" id="klassifikation2" name="klassifikation2">
                <option>1</option>
            </select>
        </div>
        <div class="form-group">
            <label for="klassifikation3">Klassifikation 3</label>
            <select class="form-control" id="klassifikation3" name="klassifikation3">
                <option>1</option>
            </select>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="abstimmung" name="abstimmung" value="1">
            <label class="form-check-label" for="abstimmung">Absstimmung</label>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Anmelden</button>
    </form>
</body>
</html>