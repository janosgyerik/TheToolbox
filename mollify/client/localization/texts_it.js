(function(){ mollify.texts.set('it', {

decimalSeparator: ",",
groupingSeparator: ".",
zeroDigit: "0",
plusSign: "+",
minusSign: "-",

fileSizeFormat: "#.#",
sizeInBytes: "{0} bytes",
sizeOneByte: "1 byte",
sizeOneKilobyte: "1 KB",
sizeInKilobytes: "{0} KB",
sizeInMegabytes: "{0} MB",
sizeInGigabytes: "{0} GB",

confirmFileDeleteMessage: "Sei sicuro di voler eliminare il file \"{0}\"?",
confirmDirectoryDeleteMessage: "Sei sicuro di voler eliminare la cartella \"{0}\" e tutti i suoi files e sottocartelle?",

uploadingNFilesInfo: "Caricando {0} files",
uploadMaxSizeHtml: "<span class=\"mollify-upload-info\">La dimensione massima di upload è <span class=\"mollify-upload-info-size\">{0}</span>, e la dimensione massima di tutti i files è <span class=\"mollify-upload-info-size\">{1}</span>.</span>",
fileUploadDialogUnallowedFileType: "Il caricamento dei files di tipo \"{0}\" non è permesso.",
fileUploadSizeTooBig: "Il file \"{0}\" con dimensione {1} eccede la dimensione massima di caricamento di {2}.",
fileUploadTotalSizeTooBig: "I files selezionati eccedono la dimensione totale massima di caricamento di {0}.",

copyFileMessage: "<span class=\"mollify-copy-file-message\">Seleziona la cartella in cui il file <span class=\"mollify-copy-file-message-file\">\"{0}\"</span> verrà copiato:</span>",
moveFileMessage: "<span class=\"mollify-move-file-message\">Seleziona la cartella in cui il file <span class=\"mollify-move-file-message-file\">\"{0}\"</span> verrà spostato:</span>",
copyDirectoryMessage: "<span class=\"mollify-copy-directory-message\">Seleziona la cartella in cui la cartella <span class=\"mollify-copy-directory-message-directory\">\"{0}\"</span> verrà copiata:</span>",
moveDirectoryMessage: "<span class=\"mollify-move-directory-message\">Seleziona la cartella in cui la cartella <span class=\"mollify-move-directory-message-directory\">\"{0}\"</span> verrà spostata:</span>",

userDirectoryListDefaultName: "{0} (Default)",

confirmMultipleItemDeleteMessage: "Sei sicuro di voler cancellare {0} elementi?",
copyMultipleItemsMessage: "<span class=\"mollify-copy-items-message\">Seleziona la cartella in cui {0} elementi verranno copiati:</span>",
moveMultipleItemsMessage: "<span class=\"mollify-move-items-message\">Seleziona la cartella in cui {0} elementi verranno spostati:</span>",

dragMultipleItems: "{0} elementi",

publicLinkMessage: "Collegamento pubblico per \"{0}\":",
copyHereDialogMessage: "<span class=\"mollify-copy-here-message\">Inserisci il nome per la copia di <span class=\"mollify-copy-file-message-file\">\"{0}\"</span>:</span>",

searchResultsInfo: "Trovati i seguenti risultati ({1}) cercando \"{0}\":",

retrieveUrlNotFound: "Risorsa non trovata: {0}",
retrieveUrlNotAuthorized: "Accesso alla risorsa non autorizzato: {0}",

shortDateTimeFormat: "dd/MM/yyyy HH:mm:ss",
shortDateFormat: "d/M/yyyy",
timeFormat: "h:mm:ss a",

pleaseWait: "Attendere prego...",

permissionModeAdmin: "Amministratore",
permissionModeReadWrite: "Lettura e scrittura",
permissionModeReadOnly: "Solo lettura",
permissionModeNone: "Nessun permesso",

loginDialogTitle: "Login",
loginDialogUsername: "Nome utente:",
loginDialogPassword: "Password:",
loginDialogRememberMe: "Ricordami",
loginDialogResetPassword: "Password dimenticata?",
loginDialogRegister: "Registrati",
loginDialogLoginButton: "Entra",
loginDialogLoginFailedMessage: "Accesso negato: nome utente o password non corretti.",

resetPasswordPopupMessage: "Per reimpostare la tua password, inserisci il tuo indirizzo email:",
resetPasswordPopupButton: "Invia",
resetPasswordPopupTitle: "Reimposta password",
resetPasswordPopupInvalidEmail: "Non ci sono utenti con questo indirizzo email",
resetPasswordPopupResetFailed: "Operazione fallita",
resetPasswordPopupResetSuccess: "La tua password è stata reimpostata. Controlla la tua casella email.",

mainViewParentDirButtonTitle: "..",
mainViewRefreshButtonTitle: "Aggiorna",
mainViewChangePasswordTitle: "Cambio password...",
mainViewAdministrationTitle: "Amministrazione...",
mainViewEditPermissionsTitle: "Permessi file...",
mainViewLogoutButtonTitle: "Logout",
mainViewAddButtonTitle: "Aggiungi",
mainViewAddFileMenuItem: "Aggiungi files...",
mainViewAddDirectoryMenuItem: "Aggiungi cartella...",
mainViewRetrieveUrlMenuItem: "Aggiungi un file da URL...",
mainViewAddButtonTooltip: "Aggiungi files o cartelle",
mainViewRefreshButtonTooltip: "Aggiorna",
mainViewParentDirButtonTooltip: "Livello superiore",
mainViewHomeButtonTooltip: "Cartelle utente",
mainViewSearchHint: "Cerca",
mainViewSlideBarTitleSelect: "Select Mode",
mainViewOptionsListTooltip: "Lista",
mainViewOptionsGridSmallTooltip: "Icone Piccole",
mainViewOptionsGridLargeTooltip: "Icone Grandi",
	
fileDetailsLabelLastAccessed: "Ultimo accesso",
fileDetailsLabelLastModified: "Ultima modifica",
fileDetailsLabelLastChanged: "Ultimo cambio",

fileDetailsAddDescription: "Aggiungi descrizione",
fileDetailsEditDescription: "Modifica",
fileDetailsApplyDescription: "Applica",
fileDetailsCancelEditDescription: "Cancella",
fileDetailsRemoveDescription: "Rimuovi",
fileDetailsEditPermissions: "Modifica permessi",
fileDetailsActionsTitle: "Azioni",

filePreviewTitle: "Anteprima",

fileActionDetailsTitle: "Dettagli",
fileActionDownloadTitle: "Download",
fileActionDownloadZippedTitle: "Download compresso",
fileActionRenameTitle: "Rinomina...",
fileActionCopyTitle: "Copia...",
fileActionCopyHereTitle: "Copia qui...",
fileActionMoveTitle: "Sposta...",
fileActionDeleteTitle: "Elimina",
fileActionViewTitle: "Visualizza",
fileActionEditTitle: "Modifica",
dirActionDownloadTitle: "Download compresso",
dirActionRenameTitle: "Rinomina...",
dirActionDeleteTitle: "Elimina",
fileActionPublicLinkTitle: "Ottieni public link...",

fileListColumnTitleSelect: "",
fileListColumnTitleName: "Nome",
fileListColumnTitleType: "Tipo",
fileListColumnTitleSize: "Dimensione",
fileListColumnTitleLastModified: "Ultima modifica",
fileListColumnTitleDescription: "Descrizione",
fileListDirectoryType: "Cartella",
fileListColumnTitleComments: "Commenti",

errorMessageRequestFailed: "Richiesta fallita",
errorMessageInvalidRequest: "Il Server non può elaborare la richiesta.",
errorMessageNoResponse: "Nessuna risposta dal server.",
errorMessageInvalidResponse: "Il server ha restituito una risposta non valida.",
errorMessageDataTypeMismatch: "Il server ha restituito un risultato inatteso.",
errorMessageOperationFailed: "Operazione fallita.",
errorMessageAuthenticationFailed: "Autenticazione fallita.",
errorMessageInvalidConfiguration: "Configurazione non valida.",
errorMessageDirectoryAlreadyExists: "Cartella già esistente.",
errorMessageFileAlreadyExists: "File già esistente.",
errorMessageDirectoryDoesNotExist: "Cartella inesistente.",
errorMessageInsufficientRights: "Permessi insufficienti per effettuare l'operazione.",
errorMessageUnknown: "Errore sconosciuto.",

directorySelectorSeparatorLabel: "/",
directorySelectorMenuPleaseWait: "Attendere prego...",
directorySelectorMenuNoItemsText: "Nessuna cartella",

infoDialogInfoTitle: "Informazione",
infoDialogErrorTitle: "Errore",

confirmationDialogYesButton: "Si",
confirmationDialogNoButton: "No",

dialogOkButton: "OK",
dialogCancelButton: "Cancella",
dialogCloseButton: "Chiudi",

deleteFileConfirmationDialogTitle: "Elimina file",
deleteDirectoryConfirmationDialogTitle: "Elimina cartella",

renameDialogTitleFile: "Rinomina file",
renameDialogTitleDirectory: "Rinomina cartella",
renameDialogOriginalName: "Nome attuale:",
renameDialogNewName: "Rinomina in:",
renameDialogRenameButton: "Rinomina",

fileUploadDialogTitle: "Upload file",
fileUploadDialogMessage: "Seleziona file(s) per upload:",
fileUploadDialogUploadButton: "Upload",
fileUploadDialogAddFileButton: "+",
fileUploadDialogAddFilesButton: "Aggiungi file(s)...",
fileUploadDialogRemoveFileButton: "-",
fileUploadDialogInfoTitle: "Dettagli",
fileUploadFileExists: "I seguenti files già esistono nella cartella: {0}",

fileUploadProgressTitle: "File in fase di upload",
fileUploadProgressPleaseWait: "Attendere prego...",
fileUploadDialogMessageFileCompleted: "Completato",
fileUploadDialogMessageFileCancelled: "Cancellato",
fileUploadTotalProgressTitle: "Totale:",
fileUploadDialogSelectFileTypesDescription: "Files consentiti",

createFolderDialogTitle: "Crea nuova cartella",
createFolderDialogName: "Nome cartella:",
createFolderDialogCreateButton: "Crea",

selectFolderDialogSelectButton: "Seleziona",
selectFolderDialogFoldersRoot: "Cartelle",
selectFolderDialogRetrievingFolders: "Attendere prego...",

selectItemDialogTitle: "Seleziona file o cartella",
selectPermissionItemDialogMessage: "Seleziona il file o la cartella per la modifica dei permessi:",
selectPermissionItemDialogAction: "Seleziona",

copyFileDialogTitle: "Copia file",
copyFileDialogAction: "Copia",

moveFileDialogTitle: "Sposta file",
moveFileDialogAction: "Sposta",

moveDirectoryDialogTitle: "Sposta cartella",
moveDirectoryDialogAction: "Sposta",

copyDirectoryDialogTitle: "Copia cartella",
copyDirectoryDialogAction: "Copia",

copyMultipleItemsTitle: "Copia elementi",
cannotCopyAllItemsMessage: "Non è possibile copiare tutti gli elementi nella cartella selezionata.",

moveMultipleItemsTitle: "Sposta elementi",
cannotMoveAllItemsMessage: "Non è possibile spostare tutti gli elementi nella cartella selezionata.",
	
passwordDialogTitle: "Modifica password",
passwordDialogOriginalPassword: "Password attuale:",
passwordDialogNewPassword: "Nuova password:",
passwordDialogConfirmNewPassword: "Conferma nuova password:",
passwordDialogChangeButton: "Modifica",
passwordDialogPasswordChangedSuccessfully: "La password è stata modificata con successo",
passwordDialogOldPasswordIncorrect: "Password attuale non corretta: la password non è stata modificata",

configurationDialogTitle: "Configurazione",
configurationDialogCloseButton: "Chiudi",
configurationDialogSettingUsers: "Utenti",
configurationDialogSettingFolders: "Cartelle pubblicate",
configurationDialogSettingUserFolders: "Cartelle utente",
configurationDialogSettingUsersViewTitle: "Utenti",
configurationDialogSettingUsersAdd: "Aggiungi...",
configurationDialogSettingUsersEdit: "Modifica...",
configurationDialogSettingUsersRemove: "Rimuovi",
configurationDialogSettingUsersResetPassword: "Reimposta password...",
configurationDialogSettingUsersCannotDeleteYourself: "Non puoi cancellare il tuo account.",

configurationDialogSettingFoldersViewTitle: "Cartelle pubblicate",
configurationDialogSettingFoldersAdd: "Aggiungi...",
configurationDialogSettingFoldersEdit: "Modifica...",
configurationDialogSettingFoldersRemove: "Rimuovi",

configurationDialogSettingUserFoldersViewTitle: "Cartelle utente",
configurationDialogSettingUserFoldersSelectUser: "Seleziona utente",
configurationDialogSettingUserFoldersAdd: "Aggiungi...",
configurationDialogSettingUserFoldersEdit: "Modifica...",
configurationDialogSettingUserFoldersRemove: "Rimuovi",
configurationDialogSettingUserFoldersNoFoldersAvailable: "Non ci sono cartelle da aggiungere per questo utente.",

userListColumnTitleName: "Nome",
userListColumnTitleType: "Tipo",
folderListColumnTitleName: "Nome",
folderListColumnTitleLocation: "Località",

userDialogAddTitle: "Aggiungi utente",
userDialogEditTitle: "Modifica utente",
userDialogUserName: "Nome:",
userDialogUserType: "Tipo:",
userDialogPassword: "Password:",
userDialogGeneratePassword: "Nuova",
userDialogAddButton: "Aggiungi",
userDialogEditButton: "Modifica",

folderDialogAddTitle: "Aggiungi cartella",
folderDialogEditTitle: "Modifica cartella",
folderDialogName: "Nome pubblico cartella:",
folderDialogPath: "Percorso cartella (non visibile agli utenti):",
folderDialogAddButton: "Aggiungi",
folderDialogEditButton: "Modifica",

resetPasswordDialogTitle: "Reimposta password",
resetPasswordDialogPassword: "Nuova password:",
resetPasswordDialogGeneratePassword: "Nuova",
resetPasswordDialogResetButton: "Reimposta",

userFolderDialogAddTitle: "Aggiungi cartella utente",
userFolderDialogEditTitle: "Modifica cartella utente",
userFolderDialogDirectoriesTitle: "Cartella:",
userFolderDialogUseDefaultName: "Usa nome di default cartella",
userFolderDialogName: "Nome pubblico cartella:",
userFolderDialogDefaultNameTitle: "Nome di default cartella:",
userFolderDialogAddButton: "Aggiungi",
userFolderDialogEditButton: "Modifica",
userFolderDialogSelectFolder: "Seleziona cartella",

invalidDescriptionUnsafeTags: "Tags HTML non permessi nella descrizione: è ammesso solo \"HTML sicuro\".",

itemPermissionEditorDialogTitle: "Modifica permessi",
itemPermissionEditorItemTitle: "Nome:",
itemPermissionEditorDefaultPermissionTitle: "Permessi di default:",
itemPermissionEditorNoPermission: "-",
itemPermissionListColumnTitleName: "Nome",
itemPermissionListColumnTitlePermission: "Permessi",
itemPermissionEditorSelectItemMessage: "Seleziona elemento",
itemPermissionEditorButtonSelectItem: "...",
itemPermissionEditorButtonAddUserPermission: "Aggiungi utente",
itemPermissionEditorButtonAddUserGroupPermission: "Aggiungi gruppo",
itemPermissionEditorButtonEditPermission: "Modifica",
itemPermissionEditorButtonRemovePermission: "Rimuovi",
itemPermissionEditorConfirmItemChange: "Ci sono modifiche non salvate: sei sicuro di voler continuare?",

fileItemUserPermissionDialogAddTitle: "Aggiungi permessi utente",
fileItemUserPermissionDialogAddGroupTitle: "Aggiungi permessi al gruppo",
fileItemUserPermissionDialogEditTitle: "Modifica permessi utente",
fileItemUserPermissionDialogEditGroupTitle: "Modifica permessi al gruppo",
fileItemUserPermissionDialogName: "Nome:",
fileItemUserPermissionDialogPermission: "Permessi:",
fileItemUserPermissionDialogAddButton: "Aggiungi",
fileItemUserPermissionDialogEditButton: "Modifica",

dropBoxTitle: "Dropbox",
dropBoxActions: "Azioni",
dropBoxActionClear: "Cancella tutto",
dropBoxActionCopy: "Copia...",
dropBoxActionCopyHere: "Copia qui",
dropBoxActionMove: "Sposta...",
dropBoxActionMoveHere: "Sposta qui",
dropBoxNoItems: "Aggiungi element qui",

mainViewSelectButton: "Seleziona",
mainViewSelectAll: "Seleziona tutto",
mainViewSelectNone: "Deseleziona",
mainViewSelectActions: "Azioni",
mainViewSelectActionAddToDropbox: "Aggiungi alla Dropbox",
mainViewDropBoxButton: "Dropbox",

fileViewerOpenInNewWindowTitle: "Apri in una nuova finestra",
fileEditorSave: "Salva",

filePublicLinkTitle: "Link pubblico",

copyHereDialogTitle: "Copia qui",

retrieveUrlTitle: "Aggiungi un file da URL",
retrieveUrlMessage: "Inserisci l'URL del file da aggiungere:",
retrieveUrlFailed: "Non è stato possibile aggiungere il file.",

searchResultsDialogTitle: "Risultati della ricerca",
searchResultsNoMatchesFound: "Nessun risultato trovato.",
searchResultListColumnTitlePath: "Percorso",
searchResultsTooltipMatches: "Corrispondenza:",
searchResultsTooltipMatchName: "Nome",
searchResultsTooltipMatchDescription: "Descrizione",

fileItemContextDataName: "Nome",
fileItemContextDataPath: "Percorso",
fileItemContextDataSize: "Dimensione file",
fileItemContextDataExtension: "Estensione",
fileItemContextDataLastModified: "Ultima modifica",
fileItemDetailsExif: "Informazioni immagine",
fileItemContextDataImageSize: "Dimensione immagine",
fileItemContextDataImageSizePixels: "{0} pixels",

commentsDialogTitle: "Commenti",
commentsDialogNoComments: "Nessun commento",
commentsDialogAddButton: "Aggiungi",
commentsDetailsCount: "Commenti",
commentsDialogRemoveComment: "Rimuovi"
})})();