<?
// ---------------------------------------------------------------------------------------------
//   Variables
// ---------------------------------------------------------------------------------------------

$MyOptTmpl=array();
$MyOptHelp=array();

$MyOptHelp[""]="";

// Prefixe des tables
$MyOptTmpl["tbl"]="p67";
$MyOptHelp["tbl"]="Prefixe des tables dans la base de donn�es";

// Site en maintenance
$MyOptTmpl["maintenance"]="off";
$MyOptHelp["maintenance"]="Mettre le site en maintenance (on=site en maintenance, off=site accessible)";

// path
$MyOptTmpl["mydir"]=htmlentities(preg_replace("/updatedb\.php/","",$_SERVER["SCRIPT_FILENAME"]));
$MyOptHelp["mydir"]="Chemin de l'installation. Utilis� pour l'ex�cution des scripts";

// Timezone
$MyOptTmpl["timezone"]=date_default_timezone_get();
$MyOptHelp["timezone"]="S�lectionner la timezone locale (Europe/Paris)";


// Devise
$MyOptTmpl["devise"]="�";
$MyOptHelp["devise"]="Devise utilis�e";

// URL
$MyOptTmpl["host"]=htmlentities($_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].preg_replace("/\/index\.php/","",$_SERVER["SCRIPT_NAME"]));
$MyOptHelp["host"]="Chemin complet du site. Utilis� pour g�n�rer les url statiques.";

// Titre du site
$MyOptTmpl["site_title"]="Polygone 67";
$MyOptHelp["site_title"]="Titre du site web";

// Logo du site dans le dossier images
$MyOptTmpl["site_logo"]="logo.gif";
$MyOptHelp["site_logo"]="Nom du fichier logo (dans le dossier images)";

// Active l'envoi de mail (0=ok, 1=nok)
$MyOptTmpl["sendmail"]="1";
$MyOptHelp["sendmail"]="Active l'envoi de mail (0=ok, 1=nok)";

$MyOptTmpl["mail"]["smtp"]="1";
$MyOptHelp["mail"]["smtp"]="Envoie des mails par SMTP (0=Sendmail, 1=SMTP)";

$MyOptTmpl["mail"]["host"]="localhost";
$MyOptHelp["mail"]["host"]="FQDN du serveur SMTP";

$MyOptTmpl["mail"]["port"]="25";
$MyOptHelp["mail"]["port"]="SMTP port";

$MyOptTmpl["mail"]["username"]="";
$MyOptHelp["mail"]["username"]="SMTP username";

$MyOptTmpl["mail"]["password"]="";
$MyOptHelp["mail"]["password"]="SMTP user password";

// Uid Banque
$MyOptTmpl["uid_banque"]=46;
$MyOptHelp["uid_banque"]="ID du compte Banque";

// Uid Club
$MyOptTmpl["uid_club"]=47;
$MyOptHelp["uid_club"]="ID du compte club";

// UID Bapteme
$MyOptTmpl["uid_bapteme"]="53";
$MyOptHelp["uid_bapteme"]="ID du compte bapteme";

// Compte par d�faut pour le tableau de bord
$MyOptTmpl["uid_tableaubord"]=$MyOptTmpl["uid_club"];
$MyOptHelp["uid_tableaubord"]="Compte par d�faut pour le tableau de bord";

// Trie par Nom ou par Pr�nom
$MyOptTmpl["globalTrie"]="prenom";
$MyOptHelp["globalTrie"]="Ordre de trie par d�fault des listes (prenom, nom,...). Mettre le nom du champs pour le trie";

// Coordonn�es terrain
$MyOptTmpl["terrain"]["nom"]="Neuhof";
$MyOptHelp["terrain"]["nom"]="Nom du terrain d'origine";
$MyOptTmpl["terrain"]["longitude"]=-7.77750;
$MyOptHelp["terrain"]["longitude"]="Longitude du terrain (n�gatif si � l'est)";
$MyOptTmpl["terrain"]["latitude"]=48.55360;
$MyOptHelp["terrain"]["latitude"]="Latitude du terrain";

// D�but et fin de la journ�e de r�servation
$MyOptTmpl["debjour"]="6";
$MyOptHelp["debjour"]="Heure du d�but de la journ�e (pour l'affichage du calendrier)";
$MyOptTmpl["finjour"]="22";
$MyOptHelp["finjour"]="Heure de fin de la journ�e";

// Unit�s
$MyOptTmpl["unitPoids"]="kg";
$MyOptHelp["unitPoids"]="Unit� des poids";
$MyOptTmpl["unitVol"]="L";
$MyOptHelp["unitVol"]="Unit� des volumes";

// Texte � accepter pour une r�servation
$MyOptTmpl["TxtValidResa"]="Pilote, il est de votre responsabilit� de v�rifier que vous respectez bien les conditions d�exp�rience r�cente pour voler sur cet avion. Au del� de 3 mois maximum sans voler : un vol avec un instructeur du club est obligatoire.<br />Confirmer que vous avez vol� depuis moins de 3 mois sur cet avion ou assimil�.";
$MyOptHelp["TxtValidResa"]="Texte � afficher si les conditions de vol pour le pilote ne sont pas satisfaites";
$MyOptTmpl["ChkValidResa"]="oui";
$MyOptHelp["ChkValidResa"]="Active l'affichage du texte � confirmer";


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

// Choix par d�faut pour l'envoie d'emails
$MyOptTmpl["typeMail"]["pilote"]="on";
$MyOptHelp["typeMail"]["pilote"]="Configure le choix par d�faut pour l'envoie des mails. 'on': on coche la case, 'vide': on ne la coche pas";
$MyOptTmpl["typeMail"]["eleve"]="on";
$MyOptTmpl["typeMail"]["instructeur"]="on";
$MyOptTmpl["typeMail"]["membre"]="";
$MyOptTmpl["typeMail"]["invite"]="";
$MyOptTmpl["typeMail"]["parent"]="";
$MyOptTmpl["typeMail"]["enfant"]="";
$MyOptTmpl["typeMail"]["employe"]="";

// Active la visualisation des membres supprim�s
$MyOptTmpl["showDesactive"]="";
$MyOptHelp["showDesactive"]="on : Affiche les membres supprim�s";

$MyOptTmpl["showSupprime"]="";
$MyOptHelp["showSupprime"]="on : Affiche les membres supprim�s";

// Nombre de lignes affich�es pour la ventilation
$MyOptTmpl["ventilationNbLigne"]="4";
$MyOptHelp["ventilationNbLigne"]="Nombre de lignes � afficher lors d'une ventilation de mouvement";

// Modules
// on : Affich� et actif
$MyOptTmpl["module"]["aviation"]="on";
$MyOptHelp["module"]["aviation"]="on : Affiche et active le module aviation";
$MyOptTmpl["module"]["compta"]="on";
$MyOptHelp["module"]["compta"]="on : Affiche et active le module comptabilit�";
$MyOptTmpl["module"]["creche"]="";
$MyOptHelp["module"]["creche"]="on : Affiche et active le module cr�che";
$MyOptTmpl["module"]["facture"]="";
$MyOptHelp["module"]["facture"]="on : Affiche et active le module facture";
$MyOptTmpl["module"]["abonnement"]="";
$MyOptHelp["module"]["abonnement"]="on : Affiche et active le module abonnement";

// D�nini les droits d'acc�s aux rubriques
// [vide] : Visible par tous
// x : Visible pour tous, y compris invit�
// - : Masqu�
// [Role] : Affich� que pour le role
$MyOptHelp["menu"]["accueil"]="Affichage des menus du site. [vide]: Visible par tous, x : visible pour tous y compris les invit�s, - : Masqu�, [Role] : Affich� uniquement pour le role correspondant";

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
$MyOptTmpl["menu"]["compta"]="AccesComptes";
$MyOptTmpl["menu"]["vols"]="";
$MyOptTmpl["menu"]["mesinfos"]="x";
$MyOptTmpl["menu"]["ressources"]="";
$MyOptTmpl["menu"]["indicateurs"]="";
$MyOptTmpl["menu"]["configuration"]="AccesConfiguration";


// Restreint la liste des membres pour les famille. Types s�par�s par des virgules (pilote,eleve)
$MyOptTmpl["restrict"]["famille"]="";
$MyOptHelp["restrict"]["famille"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les factures.
$MyOptTmpl["restrict"]["facturation"]="";
$MyOptHelp["restrict"]["facturation"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";
// Restreint la liste des membres pour les comptes.
$MyOptTmpl["restrict"]["comptes"]="";
$MyOptHelp["restrict"]["comptes"]="Restreint l'affichage de la liste des membres pour la page des famille. Saisir les types s�par�s par des virgules (pilote,eleve)";

// Saisir les vols en facturation
$MyOptTmpl["facturevol"]="";
$MyOptHelp["facturevol"]="Saisi les vols en facturation (on=Activ�)";


// Couleurs du calendrier
$MyOptTmpl["tabcolresa"]["own"]="A0E2AF";
$MyOptHelp["tabcolresa"]["own"]="Couleur pour ses r�servations";
$MyOptTmpl["tabcolresa"]["booked"]="A9D7FE";
$MyOptHelp["tabcolresa"]["booked"]="Couleur pour une r�servation autre que les siennes";
$MyOptTmpl["tabcolresa"]["meeting"]="AAFC8F";
$MyOptHelp["tabcolresa"]["meeting"]="Couleur pour une manifestation";
$MyOptTmpl["tabcolresa"]["maintconf"]="dfacac";
$MyOptHelp["tabcolresa"]["maintconf"]="Couleur pour une maintenance confirm�e";
$MyOptTmpl["tabcolresa"]["maintplan"]="eec89e";
$MyOptHelp["tabcolresa"]["maintplan"]="Couleur pour ses maintenance planifi�e";


?>
