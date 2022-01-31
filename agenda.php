<?php

$contacts = [];

// Siempre que nos llegue el array $contacts se lo asignamos a nuestro array de contactos.
// El array $contacts que nos llega por POST está formado por los inputs hidden.
if (isset($_POST['contacts'])) {
    $contacts = array_merge($contacts, $_POST['contacts']);    
}

// De la manera en la que he programado la agenda, esta condición no sería necesaria ya que siempre
// que se refresca la página los contactos se vacían y se vuelven a llenar con lo que llega por POST.
if (isset($_GET['clear_agenda'])) {
    unset($contacts);
    $contacts = [];
}

// En el array $newContact guardamos los datos del nuevo contacto que se quiere añadir.
if (isset($_POST['new_contact'])) { 
    $newContact = $_POST['new_contact'];

    // Aquí se realizan cada una de las validaciones necesarias para añadir, actualizar o borrar un contacto.
    if (empty($newContact['name'])) {
        echo "El campo nombre está vacío.";

    } elseif (!NameExistsOnAgenda($newContact['name'], $contacts) && empty($newContact['telephone'])) {
        echo "El campo teléfono no puede estar vacío. Añade un teléfono válido.";

    } elseif (NameExistsOnAgenda($newContact['name'], $contacts) && !empty($newContact['telephone'])) {
        $contacts[ContactPosition($newContact['name'], $contacts)]['telephone'] = $newContact['telephone'];

    } elseif (NameExistsOnAgenda($newContact['name'], $contacts) && empty($newContact['telephone'])) {
        unset($contacts[ContactPosition($newContact['name'], $contacts)]);

    } else {
        $contacts[] = $newContact;
    } 
}

function NameExistsOnAgenda(string $name, array $agenda): bool
{
    foreach ($agenda as $contact) {
        if ($name === $contact['name']) {
            return true;
        }
    }
    return false;
}

// En caso de que no se encuentre el contacto, se devuelve -1 porque es una posición que nunca existirá en el array.
function ContactPosition(string $name, array $agenda): int
{
    foreach ($agenda as $index => $contact) {
        if ($name === $contact['name']) {
            return $index;
        }
    }
    return -1;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style/agenda.css?v=<?php echo time(); ?>">

    <title>Agenda</title>
</head>
<body>

    <h1>Agenda</h1>

    <form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <fieldset>
            <legend>Datos agenda</legend>
            <?php if (!empty($contacts)): ?>
                <table class="center_text">
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                    </tr>
                <?php foreach ($contacts as $contact_data) : ?>                                    
                        <tr>
                            <td> <?php echo $contact_data['name'] ?> </td>
                            <td> <?php echo $contact_data['telephone'] ?> </td>
                        </tr>
                <?php endforeach; ?>
                </table>
            <?php endif ?>    
        </fieldset>    

        <fieldset class="new_contact">
            <legend>Nuevo contacto</legend>
                <div>
                    <label for="name">Nombre:</label>
                    <input type="text" name="new_contact[name]" id="name" minlength="3" maxlength="30" require>
                    

                    <label class="telephone" for="telephone">Teléfono:</label>        
                    <input type="tel" name="new_contact[telephone]" id="telephone" minlength="9" maxlength="14" require>
                    
                    <?php foreach ($contacts as $index => $contact): ?>
                        <input type="hidden" name="contacts[<?php echo $index ?>][name]" value="<?php echo $contact['name'] ?>">
                        <input type="hidden" name="contacts[<?php echo $index ?>][telephone]" value="<?php echo $contact['telephone'] ?>">                        
                    <?php endforeach; ?>
                </div>

                <div>                    
                    <input type="submit" name="submit" value="Añadir Contacto" class="button">
                    
                    <input type="reset" name="reset" value="Limpiar Campos" class="button">
                </div>    
        </fieldset>
    </form>
    
    <?php if (!empty($contacts)): ?>
        <form name="form2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">  
            <fieldset>
                <legend>Vaciar agenda</legend>
                <button type="submit" name="clear_agenda" value="true" class="clear">Vaciar Agenda</button>
            </fieldset>
        </form>
    <?php endif ?>
</body>
</html>