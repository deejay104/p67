<?
// ---------------------------------------------------------------------------------------------
//   Variables
// ---------------------------------------------------------------------------------------------

$MyOptTmpl=array();
$MyOptHelp=array();

$MyOptHelp[""]="";

// Prefixe des tables
$MyOptTmpl["tbl"]="ea";
$MyOptHelp["tbl"]="Prefixe des tables dans la base de données";

// Site en maintenance
$MyOptTmpl["maintenance"]="off";
$MyOptHelp["maintenance"]="Mettre le site en maintenance (on=site en maintenance, off=site accessible)";

// path
$MyOptTmpl["mydir"]=htmlentities(preg_replace("/[a-z]*\.php/","",$_SERVER["SCRIPT_FILENAME"]));
$MyOptHelp["mydir"]="Chemin de l'installation. Utilisé pour l'exécution des scripts";

// Timezone
$MyOptTmpl["timezone"]=date_default_timezone_get();
$MyOptHelp["timezone"]="Sélectionner la timezone locale (Europe/Paris)";


// Devise
$MyOptTmpl["devise"]="€";
$MyOptHelp["devise"]="Devise utilisée";

// URL
$MyOptTmpl["host"]=htmlentities($_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].preg_replace("/\/[a-z]*\.php/","",$_SERVER["SCRIPT_NAME"]));
$MyOptHelp["host"]="Chemin complet du site. Utilisé pour générer les url statiques.";

// Titre du site
$MyOptTmpl["site_title"]="Easy Aero";
$MyOptHelp["site_title"]="Titre du site web";

// Logo du site dans le dossier images
$MyOptTmpl["site_logo"]="logo.png";
$MyOptHelp["site_logo"]="Nom du fichier pour le logo. Il doit se trouver dans le dossier custom.";

// Active l'envoi de mail (0=ok, 1=nok)
$MyOptTmpl["sendmail"]="off";
$MyOptHelp["sendmail"]="Active l'envoi de mail (on=Activé)";

$MyOptTmpl["mail"]["smtp"]="on";
$MyOptHelp["mail"]["smtp"]="Envoie des mails par SMTP (on=SMTP sinon sendmail)";

$MyOptTmpl["mail"]["host"]="localhost";
$MyOptHelp["mail"]["host"]="FQDN du serveur SMTP";

$MyOptTmpl["mail"]["port"]="25";
$MyOptHelp["mail"]["port"]="SMTP port";

$MyOptTmpl["mail"]["username"]="";
$MyOptHelp["mail"]["username"]="SMTP username";

$MyOptTmpl["mail"]["password"]="";
$MyOptHelp["mail"]["password"]="SMTP user password";

// Uid Système
$MyOptTmpl["uid_system"]=2;
$MyOptHelp["uid_system"]="ID du compte système";

// Uid Banque
$MyOptTmpl["uid_banque"]=3;
$MyOptHelp["uid_banque"]="ID du compte Banque";

// Uid Club
$MyOptTmpl["uid_club"]=4;
$MyOptHelp["uid_club"]="ID du compte club";


// UID Bapteme
$MyOptTmpl["uid_bapteme"]=53;
$MyOptHelp["uid_bapteme"]="ID du compte bapteme";

// Compte par défaut pour le tableau de bord
$MyOptTmpl["uid_tableaubord"]=$MyOptTmpl["uid_club"];
$MyOptHelp["uid_tableaubord"]="Compte par défaut pour le tableau de bord";

// ID poste pour facturation manifestation
$MyOptTmpl["id_PosteManip"]=0;
$MyOptHelp["id_PosteManip"]="ID du poste pour facturation manifestation";

// ID poste pour factures
$MyOptTmpl["id_PosteFacture"]=0;
$MyOptHelp["id_PosteFacture"]="ID du poste pour le crédit des factures";



// Trie par Nom ou par Prénom
$MyOptTmpl["globalTrie"]="prenom";
$MyOptHelp["globalTrie"]="Ordre de trie par défault des listes (prenom, nom,...). Mettre le nom du champs pour le trie";

// Coordonnées terrain
$MyOptTmpl["terrain"]["nom"]="Neuhof";
$MyOptHelp["terrain"]["nom"]="Nom du terrain d'origine";
$MyOptTmpl["terrain"]["longitude"]=7.77750;
$MyOptHelp["terrain"]["longitude"]="Longitude du terrain (négatif si à l'est)";
$MyOptTmpl["terrain"]["latitude"]=48.55360;
$MyOptHelp["terrain"]["latitude"]="Latitude du terrain";

// Début et fin de la journée de réservation
$MyOptTmpl["debjour"]="6";
$MyOptHelp["debjour"]="Heure du début de la journée (pour l'affichage du calendrier)";
$MyOptTmpl["finjour"]="22";
$MyOptHelp["finjour"]="Heure de fin de la journée";

// Unités
$MyOptTmpl["unitPoids"]="kg";
$MyOptHelp["unitPoids"]="Unité des poids";
$MyOptTmpl["unitVol"]="L";
$MyOptHelp["unitVol"]="Unité des volumes";

// Texte à accepter pour une réservation
$MyOptTmpl["TxtValidResa"]="Pilote, il est de votre responsabilité de vérifier que vous respectez bien les conditions d’expérience récente pour voler sur cet avion. Au delà de 3 mois maximum sans voler : un vol avec un instructeur du club est obligatoire.<br />Confirmer que vous avez volé depuis moins de 3 mois sur cet avion ou assimilé.";
$MyOptHelp["TxtValidResa"]="Texte à afficher si les conditions de vol pour le pilote ne sont pas satisfaites";
$MyOptTmpl["ChkValidResa"]="on";
$MyOptHelp["ChkValidResa"]="Active l'affichage du texte à confirmer (on=Activé)";


// Liste des types de membres
$MyOptTmpl["type"]["pilote"]="on";
$MyOptHelp["type"]["pilote"]="Active (on) ou non (vide) le type de membre correspondant";
$MyOptTmpl["type"]["eleve"]="on";
$MyOptTmpl["type"]["instructeur"]="on";
$MyOptTmpl["type"]["membre"]="on";
$MyOptTmpl["type"]["invite"]="on";
$MyOptTmpl["type"]["parent"]="";
$MyOptTmpl["type"]["enfant"]="";
$MyOptTmpl["type"]["employe"]="";

// Choix par défaut pour l'envoie d'emails
$MyOptTmpl["typeMail"]["pilote"]="on";
$MyOptHelp["typeMail"]["pilote"]="Configure le choix par défaut pour l'envoie des mails. 'on': on coche la case, 'vide': on ne la coche pas";
$MyOptTmpl["typeMail"]["eleve"]="on";
$MyOptTmpl["typeMail"]["instructeur"]="on";
$MyOptTmpl["typeMail"]["membre"]="";
$MyOptTmpl["typeMail"]["invite"]="";
$MyOptTmpl["typeMail"]["parent"]="";
$MyOptTmpl["typeMail"]["enfant"]="";
$MyOptTmpl["typeMail"]["employe"]="";

// Active la visualisation des membres supprimés
$MyOptTmpl["showDesactive"]="";
$MyOptHelp["showDesactive"]="on : Affiche les membres supprimés";

$MyOptTmpl["showSupprime"]="";
$MyOptHelp["showSupprime"]="on : Affiche les membres supprimés";

// Nombre de lignes affichées pour la ventilation
$MyOptTmpl["ventilationNbLigne"]="4";
$MyOptHelp["ventilationNbLigne"]="Nombre de lignes à afficher lors d'une ventilation de mouvement";

// Documents
$MyOptTmpl["expireCache"]="0";
$MyOptHelp["expireCache"]="Si supérieur à 0, nombre d'heure durant lesquelles on garde les fichiers en cache. Si 0, on garde indéfiniment.";



// Modules
// on : Affiché et actif
$MyOptTmpl["module"]["aviation"]="on";
$MyOptHelp["module"]["aviation"]="on : Affiche et active le module aviation";
$MyOptTmpl["module"]["compta"]="on";
$MyOptHelp["module"]["compta"]="on : Affiche et active le module comptabilité";
$MyOptTmpl["module"]["creche"]="";
$MyOptHelp["module"]["creche"]="on : Affiche et active le module crèche";
$MyOptTmpl["module"]["facture"]="";
$MyOptHelp["module"]["facture"]="on : Affiche et active le module facture";
$MyOptTmpl["module"]["abonnement"]="";
$MyOptHelp["module"]["abonnement"]="on : Affiche et active le module abonnement";

// Dénini les droits d'accès aux rubriques
// [vide] : Visible par tous
// x : Visible pour tous, y compris invité
// - : Masqué
// [Role] : Affiché que pour le role
$MyOptHelp["menu"]["accueil"]="Affichage des menus du site. [vide]: Visible par tous, x : visible pour tous y compris les invités, - : Masqué, [Role] : Affiché uniquement pour le role correspondant";

$MyOptTmpl["menu"]["accueil"]="";
$MyOptTmpl["menu"]["membres"]="x";
$MyOptTmpl["menu"]["famille"]="-";
$MyOptTmpl["menu"]["presence"]="-";
$MyOptTmpl["menu"]["facture"]="-";
$MyOptTmpl["menu"]["reservations"]="";
$MyOptTmpl["menu"]["baptemes"]="AccesBaptemes";
$MyOptTmpl["menu"]["manips"]="";
$MyOptTmpl["menu"]["forums"]="";
$MyOptTmpl["menu"]["comptes"]="";
$MyOptTmpl["menu"]["suiviclub"]="AccesSuiviClub";
$MyOptTmpl["menu"]["vols"]="";
$MyOptTmpl["menu"]["mesinfos"]="x";
$MyOptTmpl["menu"]["ressources"]="";
$MyOptTmpl["menu"]["indicateurs"]="";
$MyOptTmpl["menu"]["configuration"]="AccesConfiguration";


// Restreint la liste des membres pour les famille. Types séparés par des virgules (pilote,eleve)
$MyOptTmpl["restrict"]["famille"]="";
$MyOptHelp["restrict"]["famille"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les factures.
$MyOptTmpl["restrict"]["facturation"]="";
$MyOptHelp["restrict"]["facturation"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les comptes.
$MyOptTmpl["restrict"]["comptes"]="";
$MyOptHelp["restrict"]["comptes"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types séparés par des virgules (pilote,eleve)";

// Saisir les vols en facturation
$MyOptTmpl["facturevol"]="";
$MyOptHelp["facturevol"]="Saisi les vols en facturation (on=Activé)";

// Compense le compte CLUB lors du remboursement d'une facture
$MyOptTmpl["CompenseClub"]="";
$MyOptHelp["CompenseClub"]="Compense le compte CLUB lors du remboursement d'une facture (on=Activé)";


// Couleurs du calendrier
$MyOptTmpl["tabcolresa"]["own"]="A0E2AF";
$MyOptHelp["tabcolresa"]["own"]="Couleur pour ses réservations";
$MyOptTmpl["tabcolresa"]["booked"]="A9D7FE";
$MyOptHelp["tabcolresa"]["booked"]="Couleur pour une réservation autre que les siennes";
$MyOptTmpl["tabcolresa"]["meeting"]="AAFC8F";
$MyOptHelp["tabcolresa"]["meeting"]="Couleur pour une manifestation";
$MyOptTmpl["tabcolresa"]["maintconf"]="dfacac";
$MyOptHelp["tabcolresa"]["maintconf"]="Couleur pour une maintenance confirmée";
$MyOptTmpl["tabcolresa"]["maintplan"]="eec89e";
$MyOptHelp["tabcolresa"]["maintplan"]="Couleur pour ses maintenance planifiée";


?>
