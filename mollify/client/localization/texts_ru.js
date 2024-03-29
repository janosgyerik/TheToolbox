(function(){ mollify.texts.set('ru', {

decimalSeparator: ".",
groupingSeparator: ",",
zeroDigit: "0",
plusSign: "+",
minusSign: "-",

fileSizeFormat: "#.#",
sizeInBytes: "{0} байт",
sizeOneByte: "1 байт",
sizeOneKilobyte: "1 kB",
sizeInKilobytes: "{0} kB",
sizeInMegabytes: "{0} MB",
sizeInGigabytes: "{0} GB",

confirmFileDeleteMessage: "Вы уверены что хотите удалить файл \"{0}\"?",
confirmDirectoryDeleteMessage: "Вы уверены что хотите удалить папку \"{0}\" и все внутренние файлы и подпапки?",

uploadingNFilesInfo: "Закаченные {0} файлы",
uploadMaxSizeHtml: "<span class=\"mollify-upload-info\">Максимальный размер файла для закачки <span class=\"mollify-upload-info-size\">{0}</span>, Максимальный размер всех файлов <span class=\"mollify-upload-info-size\">{1}</span>.</span>",
fileUploadDialogUnallowedFileType: "Закачка файлов типа \"{0}\" не поддерживается.",
fileUploadSizeTooBig: "Файл \"{0}\" размером {1} превышает допустимый размер в {2}.",
fileUploadTotalSizeTooBig: "Выбранные файлы превышают допустимый размер в {0}.",  

copyFileMessage: "<span class=\"mollify-copy-file-message\">Выберите папку в которую файл <span class=\"mollify-copy-file-message-file\">\"{0}\"</span> будет скопирован:</span>",
moveFileMessage: "<span class=\"mollify-move-file-message\">Выберите папку в которую файл <span class=\"mollify-move-file-message-file\">\"{0}\"</span> будет перемещен:</span>",
copyDirectoryMessage: "<span class=\"mollify-copy-directory-message\">Выберите папку в которую папка <span class=\"mollify-copy-directory-message-directory\">\"{0}\"</span> будет скопирована:</span>",
moveDirectoryMessage: "<span class=\"mollify-move-directory-message\">Выберите папку в которую папка <span class=\"mollify-move-directory-message-directory\">\"{0}\"</span> будет перемещена:</span>",

userDirectoryListDefaultName: "{0} (Default)",

confirmMultipleItemDeleteMessage: "Вы уверены что хотете удалить {0} объектов?",
copyMultipleItemsMessage: "<span class=\"mollify-copy-items-message\">Выберите папку в которую {0} объектов будут скопированы:</span>",
moveMultipleItemsMessage: "<span class=\"mollify-move-items-message\">Выберите папку в которую {0} объектов будут перемещены:</span>",

dragMultipleItems: "{0} объектов",

publicLinkMessage: "Public link for \"{0}\":",
copyHereDialogMessage: "<span class=\"mollify-copy-here-message\">Введите имя для копии <span class=\"mollify-copy-file-message-file\">\"{0}\"</span>:</span>",

searchResultsInfo: "Следующие вхождения ({1}) найдены с \"{0}\":",

retrieveUrlNotFound: "Ресурс не найден: {0}",
retrieveUrlNotAuthorized: "Неавторизованный ресурс: {0}",

shortDateTimeFormat: "M/d/yyyy h:mm:ss a",

pleaseWait: "Пожалуйста, подождите...",

permissionModeAdmin: "Администратор",
permissionModeReadWrite: "Чтение и запись",
permissionModeReadOnly: "Только чтение",
permissionModeNone: "Прав нет",

loginDialogTitle: "Вход",
loginDialogUsername: "Имя пользователя:",
loginDialogPassword: "Пароль:",
loginDialogResetPassword: "Забыли пароль?",
loginDialogLoginButton: "Войти",
loginDialogLoginFailedMessage: "Вы не вошли, имя пользователя или пароль неверны.",

resetPasswordPopupMessage: "Для сброса пароля введите свой адрес электронной почты:",
resetPasswordPopupButton: "Сбросить",
resetPasswordPopupTitle: "Сбросить пароль",
resetPasswordPopupInvalidEmail: "Пользователя с таким адресом электронной почты не найдено",
resetPasswordPopupResetFailed: "Сбросить пароль не удалось",
resetPasswordPopupResetSuccess: "Ваш пароль сброшен, проверьте электронную почту.",

mainViewParentDirButtonTitle: "..",
mainViewRefreshButtonTitle: "Обновить",
mainViewChangePasswordTitle: "Сменить пароль...",
mainViewAdministrationTitle: "Администрирование...",
mainViewEditPermissionsTitle: "Права на файлы...",
mainViewLogoutButtonTitle: "Выход",
mainViewAddButtonTitle: "Добавить",
mainViewAddFileMenuItem: "Добавить файлы...",
mainViewAddDirectoryMenuItem: "Добавить папку...",
mainViewRetrieveUrlMenuItem: "Скачать с URL...",
mainViewAddButtonTooltip: "Добавить файлы или папки",
mainViewRefreshButtonTooltip: "Обновить",
mainViewParentDirButtonTooltip: "На уровень выше",
mainViewHomeButtonTooltip: "Корневые папки",
mainViewSearchHint: "Поиск",
	
fileDetailsLabelLastAccessed: "Последний доступ",
fileDetailsLabelLastModified: "Последняя модификация",
fileDetailsLabelLastChanged: "Последнее изменение",

fileDetailsAddDescription: "Добавить описание",
fileDetailsEditDescription: "Редактировать",
fileDetailsApplyDescription: "Применить",
fileDetailsCancelEditDescription: "Отменить",
fileDetailsRemoveDescription: "Удалить",
fileDetailsEditPermissions: "Редактировать права",
fileDetailsActionsTitle: "Действия",

filePreviewTitle: "Просмотр",

fileActionDetailsTitle: "Детали",
fileActionDownloadTitle: "Скачать",
fileActionDownloadZippedTitle: "Скачать в архиве",
fileActionRenameTitle: "Переименовать...",
fileActionCopyTitle: "Копировать...",
fileActionCopyHereTitle: "Копировать сюда...",
fileActionMoveTitle: "Переместить...",
fileActionDeleteTitle: "Удалить",
fileActionViewTitle: "Вид",
dirActionDownloadTitle: "Скачать в архиве",
dirActionRenameTitle: "Переименовать...",
dirActionDeleteTitle: "Удалить",
fileActionPublicLinkTitle: "Ссылка для публикации...",

fileListColumnTitleSelect: "",
fileListColumnTitleName: "Имя",
fileListColumnTitleType: "Тип",
fileListColumnTitleSize: "Размер",
fileListDirectoryType: "Папка",

errorMessageRequestFailed: "Неудачно",
errorMessageInvalidRequest: "Сервер не может выполнить запрос.",
errorMessageNoResponse: "Невозможно получить ответ сервера.",
errorMessageInvalidResponse: "Сервер вернул некорректный ответ.",
errorMessageDataTypeMismatch: "Сервер вернул неожиданный ответ.",
errorMessageOperationFailed: "Операция не удалась.",
errorMessageAuthenticationFailed: "Сбой аутентификации.",
errorMessageInvalidConfiguration: "Аутентификация сконфигурирована неправильно.",
errorMessageDirectoryAlreadyExists: "Папка уже существует.",
errorMessageFileAlreadyExists: "Файл уже существует.",
errorMessageDirectoryDoesNotExist: "Папка не существует.",
errorMessageInsufficientRights: "Недостаточно прав для этого действия.",
errorMessageUnknown: "Неизвестная ошибка.",

directorySelectorSeparatorLabel: "/",
directorySelectorMenuPleaseWait: "Пожалуйста, ждите...",
directorySelectorMenuNoItemsText: "Папок нет",

infoDialogInfoTitle: "Информация",
infoDialogErrorTitle: "Ошибка",

confirmationDialogYesButton: "Да",
confirmationDialogNoButton: "Нет",

dialogOkButton: "OK",
dialogCancelButton: "Отменить",
dialogCloseButton: "Закрыть",

deleteFileConfirmationDialogTitle: "Удаление файла",
deleteDirectoryConfirmationDialogTitle: "Удаление папки",

renameDialogTitleFile: "Переименование файла",
renameDialogTitleDirectory: "Переименование папки",
renameDialogOriginalName: "Исходное имя:",
renameDialogNewName: "Новое имя:",
renameDialogRenameButton: "Переименовать",

fileUploadDialogTitle: "Закачка файла",
fileUploadDialogMessage: "Выберите файл(ы) для закачки:",
fileUploadDialogUploadButton: "Закачать",
fileUploadDialogAddFileButton: "+",
fileUploadDialogAddFilesButton: "Добавить файл(ы)...",
fileUploadDialogRemoveFileButton: "-",
fileUploadDialogInfoTitle: "Детали",

fileUploadProgressTitle: "Файл закачивается",
fileUploadProgressPleaseWait: "Пожалуйста, подождите...",
fileUploadDialogMessageFileCompleted: "Готово",
fileUploadDialogMessageFileCancelled: "Отменено",
fileUploadTotalProgressTitle: "Всего:",
fileUploadDialogSelectFileTypesDescription: "Разрешенные файлы",

createFolderDialogTitle: "Создание папки",
createFolderDialogName: "Имя папки:",
createFolderDialogCreateButton: "Создать",

selectFolderDialogSelectButton: "Выбрать",
selectFolderDialogFoldersRoot: "Папки",
selectFolderDialogRetrievingFolders: "Пожалуйста, подождите...",

selectItemDialogTitle: "Выбор файла или папки",
selectPermissionItemDialogMessage: "Выберите файл или папку для смены прав:",
selectPermissionItemDialogAction: "Выбрать",

copyFileDialogTitle: "Копирование файла",
copyFileDialogAction: "Копирование",

moveFileDialogTitle: "Перемещение файла",
moveFileDialogAction: "Перемещение",

moveDirectoryDialogTitle: "Перемещение папки",
moveDirectoryDialogAction: "Перемещение",

copyDirectoryDialogTitle: "Копирование папки",
copyDirectoryDialogAction: "Копирование",

copyMultipleItemsTitle: "Копирование",
cannotCopyAllItemsMessage: "Невозможно скопировать в выбранную папку.",

moveMultipleItemsTitle: "Перемещение",
cannotMoveAllItemsMessage: " Невозможно переместить всех в выбранную папку.",
	
passwordDialogTitle: "Смена пароля",
passwordDialogOriginalPassword: "Текущий пароль:",
passwordDialogNewPassword: "Новый пароль:",
passwordDialogConfirmNewPassword: "Повторите новы пароль:",
passwordDialogChangeButton: "Сменить",
passwordDialogPasswordChangedSuccessfully: "Пароль успешно изменен",
passwordDialogOldPasswordIncorrect: "Неверный текущий пароль, пароль не изменен",

configurationDialogTitle: "Конфигурация",
configurationDialogCloseButton: "Закрыть",
configurationDialogSettingUsers: "Пользователи",
configurationDialogSettingFolders: "Опубликованные папки",
configurationDialogSettingUserFolders: "Папки пользователей",
configurationDialogSettingUsersViewTitle: "Пользователи",
configurationDialogSettingUsersAdd: "Добавить...",
configurationDialogSettingUsersEdit: "Редактировать...",
configurationDialogSettingUsersRemove: "Удалить",
configurationDialogSettingUsersResetPassword: "Сменить пароль...",
configurationDialogSettingUsersCannotDeleteYourself: "Вы не можете удалить свой собственный аккаунт.",

configurationDialogSettingFoldersViewTitle: "Опубликованные папки",
configurationDialogSettingFoldersAdd: "Добавить...",
configurationDialogSettingFoldersEdit: "Редактировать...",
configurationDialogSettingFoldersRemove: "Удалить",

configurationDialogSettingUserFoldersViewTitle: "Папки пользователей",
configurationDialogSettingUserFoldersSelectUser: "Выбор пользователя",
configurationDialogSettingUserFoldersAdd: "Добавить...",
configurationDialogSettingUserFoldersEdit: "Редактировать...",
configurationDialogSettingUserFoldersRemove: "Удалить",
configurationDialogSettingUserFoldersNoFoldersAvailable: "Нет папок для этого пользователя.",

userListColumnTitleName: "Имя",
userListColumnTitleType: "Тип",
folderListColumnTitleName: "Имя",
folderListColumnTitleLocation: "Расположение",

userDialogAddTitle: "Добавление пользователя",
userDialogEditTitle: "Редактирование данных пользователя",
userDialogUserName: "Имя:",
userDialogUserType: "Тип:",
userDialogPassword: "Пароль:",
userDialogGeneratePassword: "Новый",
userDialogAddButton: "Добавить",
userDialogEditButton: "Редактировать",

folderDialogAddTitle: "Добавление папки",
folderDialogEditTitle: "Редактирование папки",
folderDialogName: "Публичное имя папки:",
folderDialogPath: "Путь к папке (скрыт от пользователей):",
folderDialogAddButton: "Добавить",
folderDialogEditButton: "Редактировать",

resetPasswordDialogTitle: "Сброс пароля",
resetPasswordDialogPassword: "Новый пароль:",
resetPasswordDialogGeneratePassword: "Новый",
resetPasswordDialogResetButton: "Сбросить",

userFolderDialogAddTitle: "Добавление папки пользователя",
userFolderDialogEditTitle: "Редактирование свойств папки",
userFolderDialogDirectoriesTitle: "Папка:",
userFolderDialogUseDefaultName: "Использовать имя по умолчанию",
userFolderDialogName: "Публичное имя папки:",
userFolderDialogDefaultNameTitle: "Имя папки по умолчанию:",
userFolderDialogAddButton: "Добавить",
userFolderDialogEditButton: "Редактировать",
userFolderDialogSelectFolder: "Выбрать папку",

invalidDescriptionUnsafeTags: "Недопустимые теги HTML в описании. Используйте только безопасные теги.",

itemPermissionEditorDialogTitle: "Редактировать права",
itemPermissionEditorItemTitle: "Имя:",
itemPermissionEditorDefaultPermissionTitle: "Права доступа по умолчанию:",
itemPermissionEditorNoPermission: "-",
itemPermissionListColumnTitleName: "Имя",
itemPermissionListColumnTitlePermission: "Разрешено",
itemPermissionEditorSelectItemMessage: "Выделить объект",
itemPermissionEditorButtonSelectItem: "...",
itemPermissionEditorButtonAddUserPermission: "Добавить пользователя",
itemPermissionEditorButtonAddUserGroupPermission: "Добавить группу",
itemPermissionEditorButtonEditPermission: "Редактировать",
itemPermissionEditorButtonRemovePermission: "Удалить",
itemPermissionEditorConfirmItemChange: "Изменения не сохранены, вы уверены, что хотите продолжить?",

fileItemUserPermissionDialogAddTitle: "Разрешить пользователю",
fileItemUserPermissionDialogAddGroupTitle: "Разрешить группе",
fileItemUserPermissionDialogEditTitle: "Изменить права пользователя",
fileItemUserPermissionDialogEditGroupTitle: "Изменить права группы",
fileItemUserPermissionDialogName: "Имя:",
fileItemUserPermissionDialogPermission: "Разрешено:",
fileItemUserPermissionDialogAddButton: "Добавить",
fileItemUserPermissionDialogEditButton: "Редактировать",

dropBoxTitle: "Буфер",
dropBoxActions: "Действия",
dropBoxActionClear: "Очистить",
dropBoxActionCopy: "Копировать...",
dropBoxActionCopyHere: "Копировать сюда",
dropBoxActionMove: "Переместить...",
dropBoxActionMoveHere: "Переместить сюда",

mainViewSelectButton: "Выделить",
mainViewSelectAll: "Выделить все",
mainViewSelectNone: "Снять выделение",
mainViewSelectActions: "Действия",
mainViewSelectActionAddToDropbox: "Добавить в буфер",
mainViewDropBoxButton: "Буфер",

fileViewerOpenInNewWindowTitle: "Открыть в новом окне",

filePublicLinkTitle: "Публичная ссылка",

copyHereDialogTitle: "Копировать сюда",

retrieveUrlTitle: "Скачать с URL",
retrieveUrlMessage: "Введите URL для скачивания:",
retrieveUrlFailed: "Не могу скачать файл.",

searchResultsDialogTitle: "Результаты поиска",
searchResultsNoMatchesFound: "Совпадений не найдено.",
searchResultListColumnTitlePath: "Путь"
})})();