INSERT INTO `lgks_admin_links` (`id`, `menuid`, `title`, `mode`, `category`, `menugroup`, `class`, `link`, `iconpath`, `tips`, `site`, `privilege`, `blocked`, `onmenu`, `target`, `device`, `to_check`, `weight`, `userid`, `doc`, `doe`) VALUES 
('101', 'admin_menu', 'Server Tools', '*', '', '', '', '#', 'icons/sidebar/sites.png', 'Manage Sites And Apps Installed', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('102', 'admin_menu', 'WebMaster Tools', '*', '', '', '', '#', 'icons/sidebar/apps.png', 'Webmaster Tools', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('103', 'admin_menu', 'Configurations', '*', '', '', '', '#', 'icons/sidebar/settings.png', 'Manage Global Configurations', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('104', 'admin_menu', 'Site Defaults', '*', '', '', '', '#', 'icons/sidebar/settings.png', 'Manage Default Configurations For All Sites', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('105', 'admin_menu', 'Privilege Manager', '*', '', '', '', '#', 'icons/sidebar/users.png', 'Manage Users/Privileges/Access', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('107', 'admin_menu', 'Package Manager', '*', '', '', '', '#', 'icons/sidebar/plugin.png', 'Install/Update Manager', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('108', 'admin_menu', 'Maintainance', '*', '', '', '', '#', 'icons/sidebar/maintainance.png', 'Maintainance Box', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('109', 'admin_menu', 'Site Reports', '*', '', '', '', '#', 'icons/sidebar/reports.png', 'Site Wide Reports', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('110', 'admin_menu', 'Advanced Tools', '*', '', '', '', '#', 'icons/sidebar/advanced.png', 'Advanced Users Tools', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('111', 'admin_menu', 'Help Manual', '*', '', '', '', '#', 'icons/sidebar/help.png', 'All In One Help Manual', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('116', 'admin_menu', 'DB Manager', '*', '', '101', '', 'page=modules&mod=dbmanager', '', 'Database Manager For All The Apps', 'admincp', '*', 'false', 'true', '', '*', 'module#dbmanager', '0', 'root', CURDATE(), CURDATE()),
('118', 'admin_menu', 'Domain Map', '*', '', '101', '', 'page=domainlist', '', 'Domain Mapping Tool To Lock Specific Domains To appSite', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('119', 'admin_menu', 'CMS Controls', '*', '', '101', '', 'page=cmscontrols', '', 'CMS Control Center', 'admincp', '*', 'false', 'true', '', 'pc', '', '0', 'root', CURDATE(), CURDATE()),
('125', 'admin_menu', 'DBLogging Settings', '*', '', '103', '', 'page=configeditor&cfg=logdb', '', 'Database Logging Settings', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('141', 'admin_menu', 'HTML Headers', '*', '', '104', '', 'page=configeditor&cfg=headers', '', 'Manage How The HTML Header Gets Printed (Apps,Meta,Keywords,Descriptions,etc..)', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('115', 'admin_menu', 'Site Manager', '*', '', '101', '', 'page=modules&mod=sitemngr', '', 'SiteCofigs/Visibility/Sitemaps/etc', 'admincp', '*', 'false', 'true', '', '*', 'module#sitemngr', '0', 'root', CURDATE(), CURDATE()),
('117', 'admin_menu', 'File Manager', '*', '', '101', '', 'page=modules&mod=fs', '', 'File Manager For Root', 'admincp', '*', 'false', 'true', '', '*', 'module#fs', '0', 'root', CURDATE(), CURDATE()),
('122', 'admin_menu', 'General Settings', '*', '', '103', '', 'page=configeditor&cfg=general', '', 'Default Sites,Languages, PHP Settings, Advanced, Permalinks, Cache, Expiry', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('123', 'admin_menu', 'System Settings', '*', '', '103', '', 'page=configeditor&cfg=system', '', 'User interfaces,UI,Templates,Dev Settings,', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('124', 'admin_menu', 'Error And Logging', '*', '', '103', '', 'page=configeditor&cfg=logging', '', 'Error And Logging Settings Manager', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('126', 'admin_menu', 'Remote Services', '*', '', '103', '', 'page=configeditor&cfg=services', '', 'Configure The Services', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('127', 'admin_menu', 'Security Settings', '*', '', '103', '', 'page=configeditor&cfg=security', '', 'Security Parameters', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('128', 'admin_menu', 'Core DB Settings', '*', '', '103', '', 'page=configeditor&cfg=db', '', 'Database Servers', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('129', 'admin_menu', 'Mail Settings', '*', '', '103', '', 'page=configeditor&cfg=mail', '', 'Configurations Manager', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('130', 'admin_menu', 'FTP Settings', '*', '', '103', '', 'page=configeditor&cfg=ftp', '', 'FTP Settings', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('131', 'admin_menu', 'Xtra Settings', '*', '', '103', '', 'page=configeditor&cfg=xtras', '', 'PCRON, Hook,Messaging', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('138', 'admin_menu', 'Default Captcha Configs', '*', '', '104', '', 'page=configeditor&cfg=captcha', '', 'Manage Captcha Configurations', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('139', 'admin_menu', 'Default Login Configs', '*', '', '104', '', 'page=configeditor&cfg=login', '', 'Login Configurations', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '0', 'root', CURDATE(), CURDATE()),
('140', 'admin_menu', 'Default Mobility Configs', '*', '', '104', '', 'page=configeditor&cfg=mobility', '', 'This changes the Mobile/Tablet Settings', 'admincp', '*', 'false', 'true', '', '*', 'module#cfgeditor', '2', 'root', CURDATE(), CURDATE()),
('142', 'admin_menu', 'Access Manager', '*', '', '105', '', 'page=siteaccesslist', '', 'How Everybody Access Your Site', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('143', 'admin_menu', 'Privilege Manager', '*', '', '105', '', 'page=modules&mod=privilegeman&type=privileges', '', 'What Everybody Accesses On Your Site', 'admincp', '*', 'false', 'true', '', '*', 'module#privilegeman', '0', 'root', CURDATE(), CURDATE()),
('144', 'admin_menu', 'User Manager', '*', '', '105', '', 'page=modules&mod=privilegeman&type=users', '', 'Users Accross All Sites.', 'admincp', '*', 'false', 'true', '', '*', 'module#privilegeman', '2', 'root', CURDATE(), CURDATE()),
('145', 'admin_menu', 'Permission Controls', '*', '', '105', '', 'page=modules&mod=privilegeman&type=roles', '', 'Manage Permission/Roles All Sites.', 'admincp', '*', 'false', 'true', '', '*', 'module#privilegeman', '2', 'root', CURDATE(), CURDATE()),
('149', 'admin_menu', 'Logiks Updater', '*', '', '108', '', 'page=modules&mod=updater', '', 'Finds And Installs Updates For Logiks Installation', 'admincp', '*', 'false', 'true', '', '*', 'module#updater,module#installer', '0', 'root', CURDATE(), CURDATE()),
('151', 'admin_menu', 'TrashBox', '*', '', '108', '', 'page=modules&mod=trashbox', '', 'Clear cache/trash/logs etc...', 'admincp', '*', 'false', 'true', '', '*', 'module#trashbox', '0', 'root', CURDATE(), CURDATE()),
('152', 'admin_menu', 'Backup/Restore Sites', '*', '', '108', '', 'page=modules&mod=sitebackup', '', 'Maintainance Kit', 'admincp', '*', 'false', 'true', '', '*', 'module#sitebackup', '0', 'root', CURDATE(), CURDATE()),
('155', 'admin_menu', 'All Log Reports', '*', '', '109', '', 'page=modules&mod=logbook&mode=manager', '', 'View from all the Log Reports Of Your System', 'admincp', '*', 'false', 'true', '', '*', 'module#logbook', '0', 'root', CURDATE(), CURDATE()),
('158', 'admin_menu', 'Activity Log', '*', '', '109', '', 'page=modules&mod=logbook&report=activity_log', '', 'Systemwide Log Reports', 'admincp', '*', 'false', 'true', '', '*', 'module#logbook', '0', 'root', CURDATE(), CURDATE()),
('159', 'admin_menu', 'Login Log', '*', '', '109', '', 'page=modules&mod=logbook&report=login_log', '', 'Systemwide Log Reports', 'admincp', '*', 'false', 'true', '', '*', 'module#logbook', '0', 'root', CURDATE(), CURDATE()),
('160', 'admin_menu', 'Error Log', '*', '', '109', '', 'page=modules&mod=logbook&report=error_log', '', 'Systemwide Log Reports', 'admincp', '*', 'false', 'true', '', '*', 'module#logbook', '0', 'root', CURDATE(), CURDATE()),
('164', 'admin_menu', 'Site Activity Chart', '*', '', '109', '', 'page=modules&mod=logbook&report=login_pie', '', 'A Pie Chart Of Login Activities Across Sites', 'admincp', '*', 'false', 'true', '', '*', 'module#logbook', '0', 'root', CURDATE(), CURDATE()),
('165', 'admin_menu', 'ChangeLog', '*', '', '109', '', 'page=modules&mod=sysreports&report=changelog', '', 'Find The File System Changes Across Time', 'admincp', '*', 'false', 'true', '', '*', 'module#sysreports', '0', 'root', CURDATE(), CURDATE()),
('166', 'admin_menu', 'PHP Info', '*', '', '109', '', 'page=modules&mod=sysreports&report=phpinfo', '', 'PHP Info', 'admincp', '*', 'false', 'true', '', '*', 'module#sysreports', '0', 'root', CURDATE(), CURDATE()),
('167', 'admin_menu', 'Disk Usage', '*', '', '109', '', 'page=modules&mod=diskusage', '', 'Reports the disk space usage for the directory you specify.', 'admincp', '*', 'false', 'true', '', '*', 'module#diskusage', '0', 'root', CURDATE(), CURDATE()),
('171', 'admin_menu', 'Apps Installer', '*', '', '107', '', 'page=installer&mode=apps', '', 'Install/Remove As Many Packages As Your Want', 'admincp', '*', 'false', 'true', '', '*', 'module#installer', '0', 'root', CURDATE(), CURDATE()),
('172', 'admin_menu', 'Plugins Installer', '*', '', '107', '', 'page=installer&mode=plugins', '', 'Install/Remove As Many Modules/Widgets As Your Want', 'admincp', '*', 'false', 'true', '', '*', 'module#installer', '0', 'root', CURDATE(), CURDATE()),
('173', 'admin_menu', 'Themes & Skins', '*', '', '107', '', 'page=installer&mode=themes', '', 'Install/Remove As Many Themes/Layouts As Your Want', 'admincp', '*', 'false', 'true', '', '*', 'module#installer', '0', 'root', CURDATE(), CURDATE()),
('175', 'admin_menu', 'Task Scheduler', '*', '', '110', '', 'page=modules&mod=cronjobs', '', 'Maintainance Kit', 'admincp', '*', 'false', 'true', '', '*', 'module#cronjobs', '0', 'root', CURDATE(), CURDATE()),
('176', 'admin_menu', 'Community Connect', '*', '', '110', '', 'page=modules&mod=commconnect', '', 'Connect To Support Community For Updates/Online Installs etc...', 'admincp', '*', 'false', 'true', '', '*', 'module#commconnect', '0', 'root', CURDATE(), CURDATE()),
('177', 'admin_menu', 'dbAdmin', '*', '', '110', '', 'page=dbadmin', '', 'Toolkit for Advanced Users Only.', 'admincp', '*', 'false', 'true', '', '*', 'module#dbedit', '0', 'root', CURDATE(), CURDATE()),
('178', 'admin_menu', 'PowerShell', '*', '', '110', '', 'page=modules&mod=konsole', '', 'Toolkit for Advanced Users Only.', 'admincp', '*', 'false', 'true', '', '*', 'module#konsole', '0', 'root', CURDATE(), CURDATE()),
('179', 'admin_menu', 'Manage Hooks', '*', '', '110', '', 'page=hookslist', '', 'Manage System/Apps Hooks', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('188', 'admin_menu', 'Help Contents', '*', '', '111', '', 'page=modules&mod=helpmanual', '', 'Please Help Me Out !!', 'admincp', '*', 'false', 'true', '', '*', 'module#helpmanual', '0', 'root', CURDATE(), CURDATE()),
('189', 'admin_menu', 'How ToS', '*', '', '111', '', 'services/?scmd=olklink&goto=howtos', '', 'Please Help Me Out !!', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('190', 'admin_menu', 'Community Blog', '*', '', '111', '', 'services/?scmd=olklink&goto=communityblog', '', 'Please Help Me Out !!', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('191', 'admin_menu', 'Report A Bug', '*', '', '111', '', 'services/?scmd=olklink&goto=bugreport', '', 'Please Help Me Out !!', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE()),
('192', 'admin_menu', 'About Logiks', '*', '', '111', '', 'page=aboutlogiks', '', 'Please Help Me Out !!', 'admincp', '*', 'false', 'true', '', '*', '', '0', 'root', CURDATE(), CURDATE());
