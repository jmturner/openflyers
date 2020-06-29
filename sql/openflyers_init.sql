-- vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: --

-- openflyers_ini.sql
--
-- sql init file
--
-- PHP versions 4 and 5
--
-- LICENSE: This program is free software; you can redistribute it and/or
-- modify it under the terms of the GNU General Public License
-- as published by the Free Software Foundation; either version 2
-- of the License, or any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program (file license.txt);
-- if not, write to the Free Software Foundation, Inc.,
-- 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
-- 
-- @category   sql
-- @author     Patrice Godard <patrice.godard@free.fr>
-- @author     Soeren MAIRE
-- @author     Christophe LARATTE <christophe.laratte@openflyers.org>
-- @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
-- @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
-- @version    CVS: $Id: openflyers_init.sql,v 1.27.4.5 2006/08/20 13:36:53 claratte Exp $
-- @link       http://openflyers.org
-- @since      Mon Sept 8 2003

-- --------------------------------------------------------

-- 
-- Structure de la table `aircraft_qualif`
-- 

CREATE TABLE `aircraft_qualif` (
  `AIRCRAFTNUM` int(10) unsigned NOT NULL default '0',
  `CHECKNUM` tinyint(3) unsigned NOT NULL default '0',
  `QUALIFID` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM COMMENT='REQUESTED QUALIFICATIONS FOR EACH AIRCRAFT';

-- 
-- Contenu de la table `aircraft_qualif`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `aircrafts`
-- 

CREATE TABLE `aircrafts` (
  `NUM` int(10) unsigned NOT NULL auto_increment,
  `CALLSIGN` varchar(255) NOT NULL default '',
  `TYPE` varchar(255) NOT NULL default '',
  `FLIGHT_HOUR_COSTS` varchar(255) NOT NULL default '',
  `SEATS_AVAILABLE` tinyint(3) unsigned NOT NULL default '0',
  `COMMENTS` varchar(255) NOT NULL default '',
  `ORDER_NUM` int(10) unsigned NOT NULL default '0',
  `non_bookable` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NUM`),
  UNIQUE KEY `ORDER_NUM` (`ORDER_NUM`),
  UNIQUE KEY `CALLSIGN` (`CALLSIGN`)
) TYPE=MyISAM COMMENT='ALL AIRCRAFTS OF ALL AIRCLUBS' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `aircrafts`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `authentication`
-- 

CREATE TABLE `authentication` (
  `NAME` varchar(255) NOT NULL default '',
  `PASSWORD` varchar(255) NOT NULL default '',
  `NUM` int(10) unsigned NOT NULL auto_increment,
  `FIRST_NAME` varchar(255) NOT NULL default '',
  `LAST_NAME` varchar(255) NOT NULL default '',
  `PROFILE` mediumint(8) unsigned NOT NULL default '0',
  `VIEW_TYPE` int(10) unsigned NOT NULL default '0',
  `VIEW_WIDTH` tinyint(3) unsigned NOT NULL default '0',
  `VIEW_HEIGHT` tinyint(4) unsigned NOT NULL default '0',
  `AIRCRAFTS_VIEWED` varchar(255) NOT NULL default '',
  `INST_VIEWED` varchar(255) NOT NULL default '',
  `EMAIL` varchar(255) NOT NULL default '',
  `TIMEZONE` varchar(255) NOT NULL default '',
  `ADDRESS` varchar(255) NOT NULL default '',
  `ZIPCODE` varchar(255) NOT NULL default '',
  `CITY` varchar(255) NOT NULL default '',
  `STATE` varchar(255) NOT NULL default '',
  `COUNTRY` varchar(255) NOT NULL default '',
  `HOME_PHONE` varchar(255) NOT NULL default '',
  `WORK_PHONE` varchar(255) NOT NULL default '',
  `CELL_PHONE` varchar(255) NOT NULL default '',
  `LANG` varchar(255) NOT NULL default '',
  `NOTIFICATION` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NUM`),
  UNIQUE KEY `NAME` (`NAME`)
) TYPE=MyISAM COMMENT='used for authentication' AUTO_INCREMENT=2 ;

-- 
-- Contenu de la table `authentication`
-- 

INSERT INTO `authentication` VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'Super', 'ADMIN', 1, 8, 10, 20, '', '', '', '', '', '', '', '', '', '', '', '', '', 0);

-- --------------------------------------------------------

-- 
-- Structure de la table `booking`
-- 

CREATE TABLE `booking` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `START_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `END_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `AIRCRAFT_NUM` int(10) unsigned NOT NULL default '0',
  `MEMBER_NUM` int(10) unsigned NOT NULL default '0',
  `SLOT_TYPE` tinyint(3) unsigned NOT NULL default '0',
  `INST_NUM` int(10) unsigned NOT NULL default '0',
  `FREE_SEATS` tinyint(3) unsigned NOT NULL default '0',
  `COMMENTS` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM COMMENT='RECORDS ALL THE SLOTS OF ALL AIRCRAFTS OF ALL AIRCLUBS' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `booking`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `clubs`
-- 

CREATE TABLE `clubs` (
  `NUM` int(10) unsigned NOT NULL default '0',
  `NAME` varchar(255) NOT NULL default '',
  `INFO_CELL` text NOT NULL,
  `LOGO` longblob NOT NULL,
  `LOGO_NAME` varchar(255) NOT NULL default '',
  `LOGO_EXT` varchar(25) NOT NULL default '',
  `LOGO_SIZE` int(11) NOT NULL default '0',
  `STYLESHEET_URL` varchar(255) NOT NULL default '',
  `FIRST_HOUR_DISPLAYED` time NOT NULL default '00:00:00',
  `LAST_HOUR_DISPLAYED` time NOT NULL default '00:00:00',
  `USUAL_PROFILES` mediumint(8) unsigned NOT NULL default '0',
  `ICAO` varchar(6) NOT NULL default '',
  `FLAGS` tinyint(3) unsigned NOT NULL default '0',
  `DEFAULT_SLOT_RANGE` int(10) unsigned NOT NULL default '0',
  `MIN_SLOT_RANGE` tinyint(3) unsigned NOT NULL default '0',
  `TWILIGHT_RANGE` tinyint(3) unsigned NOT NULL default '0',
  `MAILING_LIST_NAME` varchar(255) NOT NULL default '',
  `MAILING_LIST_TYPE` varchar(255) NOT NULL default '',
  `CLUB_SITE_URL` varchar(255) NOT NULL default '',
  `DEFAULT_TIMEZONE` varchar(255) NOT NULL default '',
  `LANG` varchar(255) NOT NULL default '',
  `ADMIN_NUM` int(10) unsigned NOT NULL default '0',
  `MAIL_FROM_ADDRESS` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`NUM`)
) TYPE=MyISAM COMMENT='DESCRIPTION OF ALL AIRCLUBS';

-- 
-- Contenu de la table `clubs`
-- 

INSERT INTO `clubs` VALUES (1, 'AC Creation', 'premier aéroclub', '', '', '', 0, '', '00:00:00', '00:00:00', 0, 'LFMA', 0, 60, 30, 30, '', '', '', '', '', 0, '');

-- --------------------------------------------------------

-- 
-- Structure de la table `exceptionnal_inst_dates`
-- 

CREATE TABLE `exceptionnal_inst_dates` (
  `INST_NUM` int(10) unsigned NOT NULL default '0',
  `START_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `END_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `PRESENCE` tinyint(3) unsigned NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Contenu de la table `exceptionnal_inst_dates`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `icao`
-- 

CREATE TABLE `icao` (
  `NAME` varchar(64) NOT NULL default '',
  `ICAO` varchar(6) NOT NULL default '',
  `LAT` varchar(7) NOT NULL default '',
  `LON` varchar(8) NOT NULL default '',
  `ALT` smallint(6) NOT NULL default '0',
  UNIQUE KEY `icao` (`ICAO`)
) TYPE=MyISAM COMMENT='airfields coord';

-- 
-- Contenu de la table `icao`
-- 

INSERT INTO `icao` VALUES ('Chelles le Pin', 'LFPH', 'N485348', 'E0023622', 208);
INSERT INTO `icao` VALUES ('Bagnoles de l''Orne Couterne', 'LFAO', 'N483244', 'W0002301', 255);
INSERT INTO `icao` VALUES ('Guéret St Laurent', 'LFCE', 'N461045', 'E0015730', 255);
INSERT INTO `icao` VALUES ('Lorient Lann Bihoué', 'LFRH', 'N474538', 'W0032624', 169);
INSERT INTO `icao` VALUES ('Nancy Azelot', 'LFEX', 'N483534', 'E0061428', 255);
INSERT INTO `icao` VALUES ('Dinan Trélivan', 'LFEB', 'N482640', 'W0020605', 255);
INSERT INTO `icao` VALUES ('Lyon Brindas', 'LFKL', 'N454242', 'E0044152', 255);
INSERT INTO `icao` VALUES ('Besançon Thise', 'LFSA', 'N471629', 'E0060503', 255);
INSERT INTO `icao` VALUES ('Paris Charles De Gaulle', 'LFPG', 'N490035', 'E0023252', 255);
INSERT INTO `icao` VALUES ('Laon Chambry', 'LFAF', 'N493545', 'E0033754', 255);
INSERT INTO `icao` VALUES ('Niort Souche', 'LFBN', 'N461848', 'W0002340', 201);
INSERT INTO `icao` VALUES ('Sézanne St Rémy', 'LFFZ', 'N484238', 'E0034551', 255);
INSERT INTO `icao` VALUES ('Sarreguemines Neunkirch', 'LFGU', 'N490741', 'E0070630', 255);
INSERT INTO `icao` VALUES ('Marmande Virazeil', 'LFDM', 'N443004', 'E0001151', 105);
INSERT INTO `icao` VALUES ('Mauléon', 'LFJB', 'N465414', 'W0004147', 255);
INSERT INTO `icao` VALUES ('Apt St Christol', 'LFXI', 'N440312', 'E0052942', 255);
INSERT INTO `icao` VALUES ('Tours Sorigny', 'LFEN', 'N471603', 'E0004204', 255);
INSERT INTO `icao` VALUES ('Chalais', 'LFIH', 'N451605', 'E0000101', 255);
INSERT INTO `icao` VALUES ('Lyon Corbas', 'LFHJ', 'N453915', 'E0045449', 255);
INSERT INTO `icao` VALUES ('Fayence', 'LFMF', 'N433632', 'E0064210', 255);
INSERT INTO `icao` VALUES ('Til Châtel', 'LFET', 'N473251', 'E0051243', 255);
INSERT INTO `icao` VALUES ('Millau Larzac', 'LFCM', 'N435921', 'E0031100', 255);
INSERT INTO `icao` VALUES ('Castres Mazamet', 'LFCK', 'N433318', 'E0021726', 255);
INSERT INTO `icao` VALUES ('Châteauroux Villers', 'LFEJ', 'N465031', 'E0013716', 255);
INSERT INTO `icao` VALUES ('Florac Ste Enimie', 'LFNO', 'N441711', 'E0032800', 255);
INSERT INTO `icao` VALUES ('Lamotte Beuvron', 'LFFM', 'N473924', 'E0015921', 255);
INSERT INTO `icao` VALUES ('Saint Flour Coltines', 'LFHQ', 'N450430', 'E0025933', 255);
INSERT INTO `icao` VALUES ('Bellegarde Vouvray', 'LFHN', 'N460727', 'E0054822', 255);
INSERT INTO `icao` VALUES ('Avignon Pujaut', 'LFNT', 'N435949', 'E0044520', 148);
INSERT INTO `icao` VALUES ('Sedan Douzy', 'LFSJ', 'N493935', 'E0050216', 255);
INSERT INTO `icao` VALUES ('Pont St Esprit', 'LFND', 'N441610', 'E0043912', 144);
INSERT INTO `icao` VALUES ('Gray St Adrien', 'LFEV', 'N472600', 'E0053722', 255);
INSERT INTO `icao` VALUES ('Juvancourt', 'LFQX', 'N480654', 'E0044915', 255);
INSERT INTO `icao` VALUES ('Saint Quentin Roupy', 'LFOW', 'N494901', 'E0031224', 255);
INSERT INTO `icao` VALUES ('Mulhouse Habsheim', 'LFGB', 'N474417', 'E0072556', 255);
INSERT INTO `icao` VALUES ('Puivert', 'LFNW', 'N425441', 'E0020322', 255);
INSERT INTO `icao` VALUES ('Cosne sur Loire', 'LFGH', 'N472138', 'E0025510', 255);
INSERT INTO `icao` VALUES ('Jonzac Neulles', 'LFCJ', 'N452903', 'W0002517', 128);
INSERT INTO `icao` VALUES ('Vierzon Méreau', 'LFFV', 'N471141', 'E0020400', 255);
INSERT INTO `icao` VALUES ('Soulac sur Mer', 'LFDK', 'N452942', 'W0010456', 7);
INSERT INTO `icao` VALUES ('Strasbourg Entzheim', 'LFST', 'N483237', 'E0073814', 255);
INSERT INTO `icao` VALUES ('Périgueux Bassillac', 'LFBX', 'N451151', 'E0004855', 255);
INSERT INTO `icao` VALUES ('Rouen Vallée de Seine', 'LFOP', 'N492327', 'E0011102', 255);
INSERT INTO `icao` VALUES ('Romorantin Pruniers', 'LFYR', 'N471915', 'E0014120', 255);
INSERT INTO `icao` VALUES ('Arbois', 'LFGD', 'N465512', 'E0054536', 255);
INSERT INTO `icao` VALUES ('Persan Beaumont', 'LFPA', 'N490954', 'E0021842', 149);
INSERT INTO `icao` VALUES ('Carpentras', 'LFNH', 'N440124', 'E0050527', 255);
INSERT INTO `icao` VALUES ('Romilly sur Seine', 'LFQR', 'N483002', 'E0034548', 255);
INSERT INTO `icao` VALUES ('Vendays Montalivet', 'LFIV', 'N452250', 'W0010657', 16);
INSERT INTO `icao` VALUES ('Chavenay Villepreux', 'LFPX', 'N485032', 'E0015849', 255);
INSERT INTO `icao` VALUES ('Joinville Mussey', 'LFFJ', 'N482310', 'E0050842', 255);
INSERT INTO `icao` VALUES ('Beaune Challanges', 'LFGF', 'N470040', 'E0045351', 255);
INSERT INTO `icao` VALUES ('Biscarrosse Parentis', 'LFBS', 'N442210', 'W0010750', 98);
INSERT INTO `icao` VALUES ('Pouilly Maconge', 'LFEP', 'N471317', 'E0043340', 255);
INSERT INTO `icao` VALUES ('Haguenau', 'LFSH', 'N484752', 'E0074913', 255);
INSERT INTO `icao` VALUES ('Ambert le Poyet', 'LFHT', 'N453101', 'E0034447', 255);
INSERT INTO `icao` VALUES ('Montagne Noire', 'LFMG', 'N432427', 'E0015925', 255);
INSERT INTO `icao` VALUES ('Cazeres Palaminy', 'LFJH', 'N431208', 'E0010304', 255);
INSERT INTO `icao` VALUES ('Calvi Ste Catherine', 'LFKC', 'N423113', 'E0084735', 209);
INSERT INTO `icao` VALUES ('Marennes', 'LFJI', 'N454924', 'W0010444', 25);
INSERT INTO `icao` VALUES ('Montpellier Candillargues', 'LFNG', 'N433637', 'E0040413', 5);
INSERT INTO `icao` VALUES ('Bitche', 'LFXG', 'N4904', 'E00727', 255);
INSERT INTO `icao` VALUES ('Bordeaux Yvrac', 'LFDY', 'N445238', 'W0002845', 240);
INSERT INTO `icao` VALUES ('Saint Simon Clastres', 'LFYT', 'N4945', 'E00313', 255);
INSERT INTO `icao` VALUES ('Phalsbourg Bourscheid', 'LFQP', 'N484605', 'E0071218', 255);
INSERT INTO `icao` VALUES ('Tours le Louroux', 'LFJT', 'N470900', 'E0004246', 255);
INSERT INTO `icao` VALUES ('Issoudun le Fay', 'LFEK', 'N465319', 'E0020229', 255);
INSERT INTO `icao` VALUES ('Bordeaux Mérignac', 'LFBD', 'N444943', 'W0004255', 161);
INSERT INTO `icao` VALUES ('Mont-Louis la Quillane', 'LFNQ', 'N423237', 'E0020712', 255);
INSERT INTO `icao` VALUES ('Mortagne au Perche', 'LFAX', 'N483225', 'E0003202', 255);
INSERT INTO `icao` VALUES ('Ste Foy la Grande', 'LFDF', 'N445113', 'E0001036', 255);
INSERT INTO `icao` VALUES ('Mantes Chérence', 'LFFC', 'N490444', 'E0014123', 255);
INSERT INTO `icao` VALUES ('Castelnau Magnoac', 'LFDQ', 'N431646', 'E0003118', 255);
INSERT INTO `icao` VALUES ('Vitry le François Vauclerc', 'LFSK', 'N484212', 'E0044104', 255);
INSERT INTO `icao` VALUES ('Saverne Steinbourg', 'LFQY', 'N484515', 'E0072535', 255);
INSERT INTO `icao` VALUES ('Bar le Duc', 'LFEU', 'N485206', 'E0051109', 255);
INSERT INTO `icao` VALUES ('Longuyon Villette', 'LFGS', 'N492904', 'E0053422', 255);
INSERT INTO `icao` VALUES ('Condom Valence sur Baise', 'LFID', 'N435437', 'E0002314', 255);
INSERT INTO `icao` VALUES ('Verdun le Rozelier', 'LFGW', 'N490720', 'E0052815', 255);
INSERT INTO `icao` VALUES ('Belle Ile', 'LFEA', 'N471936', 'W0031154', 171);
INSERT INTO `icao` VALUES ('Briare Chatillon', 'LFEI', 'N473652', 'E0024655', 255);
INSERT INTO `icao` VALUES ('Lurcy Levis', 'LFJU', 'N464246', 'E0025646', 255);
INSERT INTO `icao` VALUES ('Ribérac St Aulaye', 'LFIK', 'N451425', 'E0001601', 255);
INSERT INTO `icao` VALUES ('Clamecy', 'LFJC', 'N472618', 'E0033031', 255);
INSERT INTO `icao` VALUES ('Oyonnax Arbent', 'LFLK', 'N461645', 'E0054003', 255);
INSERT INTO `icao` VALUES ('L''Aigle St Michel', 'LFOL', 'N484535', 'E0003933', 255);
INSERT INTO `icao` VALUES ('Cherbourg Maupertus', 'LFRC', 'N493903', 'W0012831', 255);
INSERT INTO `icao` VALUES ('Saulieu Liernais', 'LFEW', 'N471422', 'E0041557', 255);
INSERT INTO `icao` VALUES ('Ghisonaccia Alzitone', 'LFKG', 'N420318', 'E0092407', 177);
INSERT INTO `icao` VALUES ('Calais Dunkerque', 'LFAC', 'N505739', 'E0015705', 11);
INSERT INTO `icao` VALUES ('Nice Côte d''Azur', 'LFMN', 'N433955', 'E0071254', 12);
INSERT INTO `icao` VALUES ('Itxassou', 'LFIX', 'N432015', 'W0012520', 255);
INSERT INTO `icao` VALUES ('Nîmes Garons', 'LFTW', 'N434527', 'E0042459', 255);
INSERT INTO `icao` VALUES ('Montaigu St Georges', 'LFFW', 'N465559', 'W0011932', 184);
INSERT INTO `icao` VALUES ('Carcassonne Salvaza', 'LFMK', 'N431257', 'E0021831', 255);
INSERT INTO `icao` VALUES ('Rethel', 'LFAP', 'N492855', 'E0042153', 255);
INSERT INTO `icao` VALUES ('Étrépagny', 'LFFY', 'N491822', 'E0013819', 255);
INSERT INTO `icao` VALUES ('Villefranche de Rouergue', 'LFCV', 'N442212', 'E0020141', 255);
INSERT INTO `icao` VALUES ('Flers Saint Paul', 'LFOG', 'N484509', 'W0003522', 255);
INSERT INTO `icao` VALUES ('Redon Bains sur Oust', 'LFER', 'N474158', 'W0020212', 223);
INSERT INTO `icao` VALUES ('Annecy Meythet', 'LFLP', 'N455551', 'E0060623', 255);
INSERT INTO `icao` VALUES ('Compiégne Margny', 'LFAD', 'N492601', 'E0024817', 255);
INSERT INTO `icao` VALUES ('Maubeuge Elesmes', 'LFQJ', 'N501833', 'E0040153', 255);
INSERT INTO `icao` VALUES ('Cambrai Niergnies', 'LFYG', 'N500833', 'E0031554', 255);
INSERT INTO `icao` VALUES ('Figeac Livernon', 'LFCF', 'N444024', 'E0014721', 255);
INSERT INTO `icao` VALUES ('La Ferté Gaucher', 'LFFG', 'N484521', 'E0031636', 255);
INSERT INTO `icao` VALUES ('Pontivy', 'LFED', 'N480328', 'W0025521', 255);
INSERT INTO `icao` VALUES ('Belfort Chaux', 'LFGG', 'N474208', 'E0064957', 255);
INSERT INTO `icao` VALUES ('Falaise', 'LFAS', 'N485538', 'W0000841', 255);
INSERT INTO `icao` VALUES ('Châteauneuf sur Cher', 'LFFU', 'N465216', 'E0022237', 255);
INSERT INTO `icao` VALUES ('Pont sur Yonne', 'LFGO', 'N481726', 'E0031503', 236);
INSERT INTO `icao` VALUES ('Montdidier', 'LFAR', 'N494023', 'E0023409', 255);
INSERT INTO `icao` VALUES ('Nogaro', 'LFCN', 'N434611', 'W0000158', 255);
INSERT INTO `icao` VALUES ('Vittel Champ de Course', 'LFSZ', 'N481326', 'E0055607', 255);
INSERT INTO `icao` VALUES ('Aubigny sur Nere', 'LFEH', 'N472850', 'E0022339', 255);
INSERT INTO `icao` VALUES ('Épinal Dogneville', 'LFSE', 'N481243', 'E0062657', 255);
INSERT INTO `icao` VALUES ('Morlaix Ploujean', 'LFRU', 'N483603', 'W0034900', 255);
INSERT INTO `icao` VALUES ('Bourg Ceyzeriat', 'LFHS', 'N461220', 'E0051730', 255);
INSERT INTO `icao` VALUES ('Saint Brieuc Armor', 'LFRT', 'N483215', 'W0025124', 255);
INSERT INTO `icao` VALUES ('Ajaccio Campo Dell''Oro', 'LFKJ', 'N415526', 'E0084809', 18);
INSERT INTO `icao` VALUES ('Neufchâteau', 'LFFT', 'N482145', 'E0054317', 255);
INSERT INTO `icao` VALUES ('Saint Chamond l''Horme', 'LFHG', 'N452935', 'E0043208', 255);
INSERT INTO `icao` VALUES ('Saint Cyr l''Ecole', 'LFPZ', 'N484837', 'E0020424', 255);
INSERT INTO `icao` VALUES ('Fréjus', 'LFTU', 'N432453', 'E0064444', 0);
INSERT INTO `icao` VALUES ('Saint Omer Wizernes', 'LFQN', 'N504346', 'E0021409', 249);
INSERT INTO `icao` VALUES ('Orléans Bricy', 'LFOJ', 'N475916', 'E0014538', 255);
INSERT INTO `icao` VALUES ('Le Valdahon', 'LFXH', 'N4710', 'E00621', 255);
INSERT INTO `icao` VALUES ('Lure Malbouhans', 'LFYL', 'N474217', 'E0063245', 255);
INSERT INTO `icao` VALUES ('Chambéry Aix les Bains', 'LFLB', 'N453821', 'E0055248', 255);
INSERT INTO `icao` VALUES ('Tournus Cuisery', 'LFFX', 'N463346', 'E0045836', 255);
INSERT INTO `icao` VALUES ('Le Mazet de Romanin', 'LFNZ', 'N434608', 'E0045337', 255);
INSERT INTO `icao` VALUES ('Aire sur l''Adour', 'LFDA', 'N434234', 'W0001443', 255);
INSERT INTO `icao` VALUES ('Muret l''Herm', 'LFBR', 'N432657', 'E0011549', 255);
INSERT INTO `icao` VALUES ('Ruoms', 'LFHF', 'N442643', 'E0042002', 255);
INSERT INTO `icao` VALUES ('Langogne Lesperon', 'LFHL', 'N444223', 'E0035318', 255);
INSERT INTO `icao` VALUES ('Pons Avy', 'LFCP', 'N453412', 'W0003054', 115);
INSERT INTO `icao` VALUES ('Berre la Fare', 'LFNR', 'N433216', 'E0051042', 108);
INSERT INTO `icao` VALUES ('Les Mureaux', 'LFXU', 'N485954', 'E0015634', 89);
INSERT INTO `icao` VALUES ('Aubenasson', 'LFJF', 'N444147', 'E0050917', 255);
INSERT INTO `icao` VALUES ('Vienne Reventin', 'LFHH', 'N452751', 'E0044946', 255);
INSERT INTO `icao` VALUES ('Le Blanc', 'LFEL', 'N463715', 'E0010515', 255);
INSERT INTO `icao` VALUES ('Lessay', 'LFOM', 'N491211', 'W0013024', 92);
INSERT INTO `icao` VALUES ('Cassagnes Begonhes', 'LFIG', 'N441046', 'E0023107', 255);
INSERT INTO `icao` VALUES ('Berck sur Mer', 'LFAM', 'N502526', 'E0013536', 30);
INSERT INTO `icao` VALUES ('Vauville', 'LFAU', 'N493727', 'W0014945', 255);
INSERT INTO `icao` VALUES ('Reims Prunay', 'LFQA', 'N491231', 'E0040924', 255);
INSERT INTO `icao` VALUES ('Couhé Vérac', 'LFDV', 'N461622', 'E0001126', 255);
INSERT INTO `icao` VALUES ('Saint Martin de Londres', 'LFNL', 'N434803', 'E0034657', 255);
INSERT INTO `icao` VALUES ('Bagnères de Luchon', 'LFCB', 'N424800', 'E0003600', 255);
INSERT INTO `icao` VALUES ('Bédarieux la Tour', 'LFNX', 'N433827', 'E0030844', 255);
INSERT INTO `icao` VALUES ('Meaux Esbly', 'LFPE', 'N485537', 'E0025002', 220);
INSERT INTO `icao` VALUES ('Ambérieu', 'LFXA', 'N455847', 'E0052016', 255);
INSERT INTO `icao` VALUES ('Montélimar Ancone', 'LFLQ', 'N443501', 'E0044426', 240);
INSERT INTO `icao` VALUES ('Bailleau Armenonville', 'LFFL', 'N483057', 'E0013824', 255);
INSERT INTO `icao` VALUES ('Avallon', 'LFGE', 'N473011', 'E0035358', 255);
INSERT INTO `icao` VALUES ('Soissons Courmelles', 'LFJS', 'N492045', 'E0031703', 255);
INSERT INTO `icao` VALUES ('La Grand''Combe', 'LFTN', 'N441440', 'E0040044', 255);
INSERT INTO `icao` VALUES ('Pézenas Nizas', 'LFNP', 'N433021', 'E0032449', 255);
INSERT INTO `icao` VALUES ('Nuits St Georges', 'LFGZ', 'N470835', 'E0045809', 255);
INSERT INTO `icao` VALUES ('Chalons Vatry', 'LFOK', 'N484624', 'E0041222', 255);
INSERT INTO `icao` VALUES ('Paris Orly', 'LFPO', 'N484324', 'E0022246', 255);
INSERT INTO `icao` VALUES ('Le Mans Arnage', 'LFRM', 'N475655', 'E0001206', 194);
INSERT INTO `icao` VALUES ('Argentan', 'LFAJ', 'N484238', 'E0000014', 255);
INSERT INTO `icao` VALUES ('Loudun', 'LFDL', 'N470214', 'E0000605', 255);
INSERT INTO `icao` VALUES ('La Ferté Alais', 'LFFQ', 'N482952', 'E0022036', 255);
INSERT INTO `icao` VALUES ('Ussel Thalamy', 'LFCU', 'N453213', 'E0022532', 255);
INSERT INTO `icao` VALUES ('Égletons', 'LFDE', 'N452517', 'E0020408', 255);
INSERT INTO `icao` VALUES ('Vichy Charmeil', 'LFLV', 'N461018', 'E0032415', 255);
INSERT INTO `icao` VALUES ('Étampes Mondésir', 'LFOX', 'N482252', 'E0020426', 255);
INSERT INTO `icao` VALUES ('Arras Roclincourt', 'LFQD', 'N501929', 'E0024815', 255);
INSERT INTO `icao` VALUES ('Albertville', 'LFKA', 'N453738', 'E0061947', 255);
INSERT INTO `icao` VALUES ('Puimoisson', 'LFTP', 'N435213', 'E0060953', 255);
INSERT INTO `icao` VALUES ('Autun Bellevue', 'LFQF', 'N465821', 'E0041544', 255);
INSERT INTO `icao` VALUES ('Saint Girons Antichan', 'LFCG', 'N430032', 'E0010616', 255);
INSERT INTO `icao` VALUES ('Saint Jean d''Angély', 'LFIY', 'N455759', 'W0003131', 246);
INSERT INTO `icao` VALUES ('Ouessant', 'LFEC', 'N482748', 'W0050349', 142);
INSERT INTO `icao` VALUES ('Lapalisse Périgny', 'LFHX', 'N461514', 'E0033519', 255);
INSERT INTO `icao` VALUES ('Pérouges Meximieux', 'LFHC', 'N455211', 'E0051114', 255);
INSERT INTO `icao` VALUES ('Pithiviers', 'LFFP', 'N480926', 'E0021133', 255);
INSERT INTO `icao` VALUES ('Feurs Chambéon', 'LFLZ', 'N454213', 'E0041204', 255);
INSERT INTO `icao` VALUES ('Vitry en Artois', 'LFQS', 'N502018', 'E0025936', 174);
INSERT INTO `icao` VALUES ('Sollières Sardières', 'LFKD', 'N451523', 'E0064805', 255);
INSERT INTO `icao` VALUES ('Issoire le Broc', 'LFHA', 'N453054', 'E0031603', 255);
INSERT INTO `icao` VALUES ('Moret Episy', 'LFPU', 'N482031', 'E0024758', 253);
INSERT INTO `icao` VALUES ('Fumel Montayral', 'LFDX', 'N442749', 'E0010028', 255);
INSERT INTO `icao` VALUES ('Nîmes Courbessac', 'LFME', 'N435114', 'E0042449', 197);
INSERT INTO `icao` VALUES ('Belley Peyrieu', 'LFKY', 'N454142', 'E0054134', 255);
INSERT INTO `icao` VALUES ('Les Sables d'' Olonne Talmont', 'LFOO', 'N462837', 'W0014322', 105);
INSERT INTO `icao` VALUES ('Brioude Beaumont', 'LFHR', 'N451930', 'E0032133', 255);
INSERT INTO `icao` VALUES ('Thionville Yutz', 'LFGV', 'N492117', 'E0061205', 255);
INSERT INTO `icao` VALUES ('Joigny', 'LFGK', 'N475942', 'E0032331', 255);
INSERT INTO `icao` VALUES ('Corté', 'LFKT', 'N421727', 'E0091138', 255);
INSERT INTO `icao` VALUES ('Nancy Malzeville', 'LFEZ', 'N484328', 'E0061228', 255);
INSERT INTO `icao` VALUES ('Saint Jean en Royans', 'LFKE', 'N450140', 'E0051836', 255);
INSERT INTO `icao` VALUES ('Saint Junien', 'LFBJ', 'N455412', 'E0005512', 255);
INSERT INTO `icao` VALUES ('Rennes St Jacques', 'LFRN', 'N480419', 'W0014356', 124);
INSERT INTO `icao` VALUES ('Figari Sud Corse', 'LFKF', 'N413008', 'E0090548', 87);
INSERT INTO `icao` VALUES ('Péronne St Quentin', 'LFAG', 'N495208', 'E0030147', 255);
INSERT INTO `icao` VALUES ('Villerupt', 'LFAW', 'N492441', 'E0055326', 255);
INSERT INTO `icao` VALUES ('Deauville St Gatien', 'LFRG', 'N492148', 'E0000936', 255);
INSERT INTO `icao` VALUES ('La Môle St Tropez', 'LFTZ', 'N431223', 'E0062857', 59);
INSERT INTO `icao` VALUES ('Langres Rolampont', 'LFSU', 'N475756', 'E0051742', 255);
INSERT INTO `icao` VALUES ('Champagnole Crotenay', 'LFGX', 'N464552', 'E0054915', 255);
INSERT INTO `icao` VALUES ('Le Puy Loudes', 'LFHP', 'N450447', 'E0034548', 255);
INSERT INTO `icao` VALUES ('Morestel', 'LFHI', 'N454116', 'E0052713', 255);
INSERT INTO `icao` VALUES ('Château Arnoux St Auban', 'LFMX', 'N440336', 'E0055929', 255);
INSERT INTO `icao` VALUES ('Oloron Herrere', 'LFCO', 'N430953', 'W0003337', 255);
INSERT INTO `icao` VALUES ('Buno Bonnevaux', 'LFFB', 'N482104', 'E0022532', 255);
INSERT INTO `icao` VALUES ('Dax Seyresse', 'LFBY', 'N434121', 'W0010408', 108);
INSERT INTO `icao` VALUES ('Poitiers Biard', 'LFBI', 'N463515', 'E0001824', 255);
INSERT INTO `icao` VALUES ('Saint Rambert d''Albon', 'LFLR', 'N451522', 'E0044933', 255);
INSERT INTO `icao` VALUES ('Pierrelatte', 'LFHD', 'N442356', 'E0044305', 197);
INSERT INTO `icao` VALUES ('Ploërmel Loyat', 'LFRP', 'N480010', 'W0022238', 236);
INSERT INTO `icao` VALUES ('Méribel', 'LFKX', 'N452425', 'E0063450', 255);
INSERT INTO `icao` VALUES ('Albi le Sequestre', 'LFCI', 'N435448', 'E0020700', 255);
INSERT INTO `icao` VALUES ('Albert Bray', 'LFAQ', 'N495821', 'E0024129', 255);
INSERT INTO `icao` VALUES ('Montendre Marcillac', 'LFDC', 'N451628', 'W0002708', 148);
INSERT INTO `icao` VALUES ('Mont Dauphin St Crépin', 'LFNC', 'N444206', 'E0063601', 255);
INSERT INTO `icao` VALUES ('Paray le Monial', 'LFGN', 'N462804', 'E0040806', 255);
INSERT INTO `icao` VALUES ('Lézignan Corbières', 'LFMZ', 'N431033', 'E0024401', 207);
INSERT INTO `icao` VALUES ('Montbéliard Courcelles', 'LFSM', 'N472912', 'E0064729', 255);
INSERT INTO `icao` VALUES ('Montceau les Mines', 'LFGM', 'N463615', 'E0042002', 255);
INSERT INTO `icao` VALUES ('Chauvigny', 'LFDW', 'N463501', 'E0003833', 255);
INSERT INTO `icao` VALUES ('Uzès', 'LFNU', 'N440505', 'E0042343', 255);
INSERT INTO `icao` VALUES ('Le Havre St Romain', 'LFOY', 'N493241', 'E0002140', 255);
INSERT INTO `icao` VALUES ('Aspres sur Buech', 'LFNJ', 'N443108', 'E0054415', 255);
INSERT INTO `icao` VALUES ('La Réole Floudes', 'LFDR', 'N443405', 'W0000322', 43);
INSERT INTO `icao` VALUES ('Châteauroux Déols', 'LFLX', 'N465137', 'E0014316', 255);
INSERT INTO `icao` VALUES ('Châtellerault', 'LFCA', 'N464653', 'E0003307', 207);
INSERT INTO `icao` VALUES ('Corlier', 'LFJD', 'N460223', 'E0052949', 255);
INSERT INTO `icao` VALUES ('La Motte Chalancon', 'LFJE', 'N442959', 'E0052412', 255);
INSERT INTO `icao` VALUES ('Saint Dié Remomeix', 'LFGY', 'N481602', 'E0070031', 255);
INSERT INTO `icao` VALUES ('Cholet le Pontreau', 'LFOU', 'N470455', 'W0005238', 255);
INSERT INTO `icao` VALUES ('Saint Pierre d''Oléron', 'LFDP', 'N455733', 'W0011858', 20);
INSERT INTO `icao` VALUES ('La Flèche Thorée les Pins', 'LFAL', 'N474139', 'E0000012', 121);
INSERT INTO `icao` VALUES ('Marville Montmédy', 'LFYK', 'N492733', 'E0052409', 255);
INSERT INTO `icao` VALUES ('Épernay Plivot', 'LFSW', 'N490019', 'E0040513', 255);
INSERT INTO `icao` VALUES ('Gaillac Lisle sur Tarn', 'LFDG', 'N435302', 'E0015232', 255);
INSERT INTO `icao` VALUES ('Sarrebourg Buhl', 'LFGT', 'N484308', 'E0070446', 255);
INSERT INTO `icao` VALUES ('Mende Brénoux', 'LFNB', 'N443015', 'E0033139', 255);
INSERT INTO `icao` VALUES ('Quiberon', 'LFEQ', 'N472856', 'W0030600', 37);
INSERT INTO `icao` VALUES ('Chambéry Challes les Eaux', 'LFLE', 'N453341', 'E0055837', 255);
INSERT INTO `icao` VALUES ('Metz Nancy Lorraine', 'LFJL', 'N485842', 'E0061448', 255);
INSERT INTO `icao` VALUES ('Auxerre Branches', 'LFLA', 'N475047', 'E0032948', 255);
INSERT INTO `icao` VALUES ('Saint Affrique Belmont', 'LFIF', 'N434927', 'E0024455', 255);
INSERT INTO `icao` VALUES ('Beynes Thiverval', 'LFPF', 'N485037', 'E0015432', 255);
INSERT INTO `icao` VALUES ('Lunéville Croismare', 'LFQC', 'N483541', 'E0063236', 255);
INSERT INTO `icao` VALUES ('Dijon Darois', 'LFGI', 'N472313', 'E0045653', 255);
INSERT INTO `icao` VALUES ('Istres le Tubé', 'LFMI', 'N433121', 'E0045527', 82);
INSERT INTO `icao` VALUES ('La Tour du Pin', 'LFKP', 'N453336', 'E0052305', 255);
INSERT INTO `icao` VALUES ('Bâle Mulhouse', 'LFSB', 'N473524', 'E0073145', 255);
INSERT INTO `icao` VALUES ('Saint Galmier', 'LFKM', 'N453626', 'E0041821', 255);
INSERT INTO `icao` VALUES ('Saint Gaudens Montrejeau', 'LFIM', 'N430631', 'E0003713', 255);
INSERT INTO `icao` VALUES ('Dinard Pleurtuit St Malo', 'LFRD', 'N483516', 'W0020448', 211);
INSERT INTO `icao` VALUES ('Dole Tavaux', 'LFGJ', 'N470234', 'E0052606', 255);
INSERT INTO `icao` VALUES ('Doncourt les Conflans', 'LFGR', 'N490910', 'E0055558', 255);
INSERT INTO `icao` VALUES ('Creil', 'LFPC', 'N491504', 'E0023119', 255);
INSERT INTO `icao` VALUES ('Saint Nazaire Montoir', 'LFRZ', 'N471838', 'W0020924', 13);
INSERT INTO `icao` VALUES ('Saint Etienne Bouthéon', 'LFMH', 'N453203', 'E0041750', 255);
INSERT INTO `icao` VALUES ('Saint Florentin Cheu', 'LFGP', 'N475856', 'E0034642', 255);
INSERT INTO `icao` VALUES ('Cognac Châteaubernard', 'LFBG', 'N453930', 'W0001903', 102);
INSERT INTO `icao` VALUES ('Metz Frescaty', 'LFSF', 'N490435', 'E0060802', 255);
INSERT INTO `icao` VALUES ('Reims Champagne', 'LFSR', 'N491837', 'E0040303', 255);
INSERT INTO `icao` VALUES ('Abbeville', 'LFOI', 'N500835', 'E0014957', 220);
INSERT INTO `icao` VALUES ('Agen la Garenne', 'LFBA', 'N441029', 'E0003526', 204);
INSERT INTO `icao` VALUES ('Aix les Milles', 'LFMA', 'N433006', 'E0052215', 255);
INSERT INTO `icao` VALUES ('Alençon Valframbert', 'LFOF', 'N482651', 'E0000633', 255);
INSERT INTO `icao` VALUES ('Amboise Dierre', 'LFEF', 'N472029', 'E0005633', 180);
INSERT INTO `icao` VALUES ('Ancenis', 'LFFI', 'N472429', 'W0011039', 111);
INSERT INTO `icao` VALUES ('Angers Marcé', 'LFJR', 'N473337', 'W0001844', 194);
INSERT INTO `icao` VALUES ('Angoulême Brie Champniers', 'LFBU', 'N454346', 'E0001309', 255);
INSERT INTO `icao` VALUES ('Arcachon la Teste de Buch', 'LFCH', 'N443555', 'W0010653', 49);
INSERT INTO `icao` VALUES ('Argenton sur Creuse', 'LFEG', 'N463549', 'E0013609', 255);
INSERT INTO `icao` VALUES ('Aurillac', 'LFLW', 'N445351', 'E0022500', 255);
INSERT INTO `icao` VALUES ('Avignon Caumont', 'LFMV', 'N435424', 'E0045407', 124);
INSERT INTO `icao` VALUES ('Avranches le Val St Père', 'LFRW', 'N483942', 'W0012416', 26);
INSERT INTO `icao` VALUES ('Barcelonnette St Pons', 'LFMR', 'N442318', 'E0063637', 255);
INSERT INTO `icao` VALUES ('Bar sur Seine', 'LFFR', 'N480401', 'E0042449', 255);
INSERT INTO `icao` VALUES ('Belleville Villié Morgon', 'LFHW', 'N460834', 'E0044253', 255);
INSERT INTO `icao` VALUES ('Belvès St Pardoux', 'LFIB', 'N444657', 'E0005732', 255);
INSERT INTO `icao` VALUES ('Bergerac Roumaniere', 'LFBE', 'N444928', 'E0003114', 171);
INSERT INTO `icao` VALUES ('Bernay St Martin', 'LFPD', 'N490610', 'E0003400', 255);
INSERT INTO `icao` VALUES ('Béziers Vias', 'LFMU', 'N431924', 'E0032112', 56);
INSERT INTO `icao` VALUES ('Biarritz Bayonne Anglet', 'LFBZ', 'N432806', 'W0013152', 245);
INSERT INTO `icao` VALUES ('Blois le Breuil', 'LFOQ', 'N474047', 'E0011221', 255);
INSERT INTO `icao` VALUES ('Brest Guipavas', 'LFRB', 'N482650', 'W0042518', 255);
INSERT INTO `icao` VALUES ('Brienne le Château', 'LFFN', 'N482551', 'E0042857', 255);
INSERT INTO `icao` VALUES ('Caen Carpiquet', 'LFRK', 'N491024', 'W0002700', 255);
INSERT INTO `icao` VALUES ('Cahors Lalbenque', 'LFCC', 'N442102', 'E0012843', 255);
INSERT INTO `icao` VALUES ('Cannes Mandelieu', 'LFMD', 'N433251', 'E0065719', 13);
INSERT INTO `icao` VALUES ('Castelsarrasin Moissac', 'LFCX', 'N440509', 'E0010738', 243);
INSERT INTO `icao` VALUES ('Châlons Ecury sur Coole', 'LFQK', 'N485422', 'E0042115', 255);
INSERT INTO `icao` VALUES ('Châteaubriant Pouancé', 'LFTQ', 'N474426', 'W0011117', 255);
INSERT INTO `icao` VALUES ('Château Thierry Belleau', 'LFFH', 'N490402', 'E0032125', 255);
INSERT INTO `icao` VALUES ('Châtillon sur Seine', 'LFQH', 'N475047', 'E0043450', 255);
INSERT INTO `icao` VALUES ('Chaumont Semoutiers', 'LFJA', 'N480530', 'E0050300', 255);
INSERT INTO `icao` VALUES ('Chartres Champhol', 'LFOR', 'N482732', 'E0013126', 255);
INSERT INTO `icao` VALUES ('Clermont Ferrand Auvergne', 'LFLC', 'N454709', 'E0030945', 255);
INSERT INTO `icao` VALUES ('Coulommiers Voisins', 'LFPK', 'N485015', 'E0030052', 255);
INSERT INTO `icao` VALUES ('Courchevel', 'LFLJ', 'N452348', 'E0063801', 255);
INSERT INTO `icao` VALUES ('Dieuze Guéblange', 'LFQZ', 'N484631', 'E0064255', 255);
INSERT INTO `icao` VALUES ('Dijon Longvic', 'LFSD', 'N471557', 'E0050542', 255);
INSERT INTO `icao` VALUES ('Dunkerque Les Moeres', 'LFAK', 'N510226', 'E0023301', 0);
INSERT INTO `icao` VALUES ('Enghien Moisselles', 'LFFE', 'N490247', 'E0022111', 255);
INSERT INTO `icao` VALUES ('Aubenas Vals Lanas', 'LFHO', 'N443222', 'E0042218', 255);
INSERT INTO `icao` VALUES ('Eu Mers le Tréport', 'LFAE', 'N500409', 'E0012536', 255);
INSERT INTO `icao` VALUES ('Charleville Mézieres', 'LFQV', 'N494706', 'E0043834', 255);
INSERT INTO `icao` VALUES ('Fontenay le Comte', 'LFFK', 'N462629', 'W0004734', 85);
INSERT INTO `icao` VALUES ('Fontenay Trésigny', 'LFPQ', 'N484226', 'E0025416', 255);
INSERT INTO `icao` VALUES ('Gap Tallard', 'LFNA', 'N442718', 'E0060216', 255);
INSERT INTO `icao` VALUES ('Granville', 'LFRF', 'N485258', 'W0013350', 45);
INSERT INTO `icao` VALUES ('Graulhet Montdragon', 'LFCQ', 'N434612', 'E0020032', 255);
INSERT INTO `icao` VALUES ('Grenoble le Versoud', 'LFLG', 'N451309', 'E0055059', 255);
INSERT INTO `icao` VALUES ('Grenoble St Geoirs', 'LFLS', 'N452147', 'E0051958', 255);
INSERT INTO `icao` VALUES ('Hyéres le Palyvestre', 'LFTH', 'N430550', 'E0060846', 7);
INSERT INTO `icao` VALUES ('Castelnaudary Villeneuve', 'LFMW', 'N431844', 'E0015516', 255);
INSERT INTO `icao` VALUES ('La Baule Escoublac', 'LFRE', 'N471722', 'W0022047', 105);
INSERT INTO `icao` VALUES ('Andernos les Bains', 'LFCD', 'N444522', 'W0010348', 66);
INSERT INTO `icao` VALUES ('Brive la Roche', 'LFBV', 'N450859', 'E0012828', 255);
INSERT INTO `icao` VALUES ('Lannion', 'LFRO', 'N484518', 'W0032828', 255);
INSERT INTO `icao` VALUES ('La Rochelle Ile de Ré', 'LFBH', 'N461045', 'W0011143', 74);
INSERT INTO `icao` VALUES ('Laval Entrammes', 'LFOV', 'N480156', 'W0004434', 255);
INSERT INTO `icao` VALUES ('Le Castellet', 'LFMQ', 'N431508', 'E0054710', 255);
INSERT INTO `icao` VALUES ('Le Havre Octeville', 'LFOH', 'N493202', 'E0000517', 255);
INSERT INTO `icao` VALUES ('Le Plessis Belleville', 'LFPP', 'N490636', 'E0024417', 255);
INSERT INTO `icao` VALUES ('Lesparre St Laurent Medoc', 'LFDU', 'N451152', 'W0005256', 105);
INSERT INTO `icao` VALUES ('Le Touquet Paris plage', 'LFAT', 'N503053', 'E0013739', 36);
INSERT INTO `icao` VALUES ('Libourne Artigues de Lussac', 'LFDI', 'N445906', 'W0000810', 157);
INSERT INTO `icao` VALUES ('Limoges Bellegarde', 'LFBL', 'N455139', 'E0011049', 255);
INSERT INTO `icao` VALUES ('Lons le Saulnier Courlaoux', 'LFGL', 'N464034', 'E0052816', 255);
INSERT INTO `icao` VALUES ('Lyon Bron', 'LFLY', 'N454346', 'E0045620', 255);
INSERT INTO `icao` VALUES ('Lyon Saint Exupéry', 'LFLL', 'N454332', 'E0050452', 255);
INSERT INTO `icao` VALUES ('Marseille Provence', 'LFML', 'N432612', 'E0051254', 70);
INSERT INTO `icao` VALUES ('Dreux Vernouillet', 'LFON', 'N484224', 'E0012146', 255);
INSERT INTO `icao` VALUES ('Royan Médis', 'LFCY', 'N453752', 'W0005832', 72);
INSERT INTO `icao` VALUES ('Montargis Vimory', 'LFEM', 'N475738', 'E0024109', 255);
INSERT INTO `icao` VALUES ('Montauban', 'LFDB', 'N440139', 'E0012242', 255);
INSERT INTO `icao` VALUES ('Montluçon Domérat', 'LFLT', 'N462113', 'E0023420', 255);
INSERT INTO `icao` VALUES ('Montluçon Guéret', 'LFBK', 'N461334', 'E0022146', 255);
INSERT INTO `icao` VALUES ('Montpellier Méditerranée', 'LFMT', 'N433500', 'E0035741', 17);
INSERT INTO `icao` VALUES ('Nancy Essey', 'LFSN', 'N484132', 'E0061334', 255);
INSERT INTO `icao` VALUES ('Nangis les Loges', 'LFAI', 'N483545', 'E0030051', 255);
INSERT INTO `icao` VALUES ('Nantes Atlantique', 'LFRS', 'N470925', 'W0013628', 90);
INSERT INTO `icao` VALUES ('Nevers Fourchambault', 'LFQG', 'N470013', 'E0030639', 255);
INSERT INTO `icao` VALUES ('Dieppe St Aubin', 'LFAB', 'N495257', 'E0010507', 255);
INSERT INTO `icao` VALUES ('Orléans St Denis de l''Hôtel', 'LFOZ', 'N475351', 'E0020951', 255);
INSERT INTO `icao` VALUES ('Pamiers les Pujols', 'LFDJ', 'N430526', 'E0014145', 255);
INSERT INTO `icao` VALUES ('Pau Pyrénées', 'LFBP', 'N432248', 'W0002507', 255);
INSERT INTO `icao` VALUES ('Épinal Mirecourt', 'LFSG', 'N481930', 'E0060400', 255);
INSERT INTO `icao` VALUES ('Perpignan Rivesaltes', 'LFMP', 'N424427', 'E0025211', 144);
INSERT INTO `icao` VALUES ('Peyresourde Balestas', 'LFIP', 'N424749', 'E0002608', 255);
INSERT INTO `icao` VALUES ('Pontarlier', 'LFSP', 'N465434', 'E0061948', 255);
INSERT INTO `icao` VALUES ('Melun Villaroche', 'LFPM', 'N483619', 'E0024015', 255);
INSERT INTO `icao` VALUES ('Pont St Vincent', 'LFSV', 'N483605', 'E0060328', 255);
INSERT INTO `icao` VALUES ('Propriano Tavaria', 'LFKO', 'N413941', 'E0085342', 13);
INSERT INTO `icao` VALUES ('Quimper Pluguffan', 'LFRQ', 'N475830', 'W0041004', 255);
INSERT INTO `icao` VALUES ('Rochefort St Agnant', 'LFDN', 'N455322', 'W0005857', 56);
INSERT INTO `icao` VALUES ('Revel Montgey', 'LFIR', 'N432853', 'E0015848', 255);
INSERT INTO `icao` VALUES ('Rion des Landes', 'LFIL', 'N435457', 'W0005657', 255);
INSERT INTO `icao` VALUES ('Roanne Renaison', 'LFLO', 'N460310', 'E0035959', 255);
INSERT INTO `icao` VALUES ('Macon Charnay', 'LFLM', 'N461745', 'E0044745', 255);
INSERT INTO `icao` VALUES ('Rodez Marcillac', 'LFCR', 'N442427', 'E0022900', 255);
INSERT INTO `icao` VALUES ('Romans St Paul', 'LFHE', 'N450358', 'E0050612', 255);
INSERT INTO `icao` VALUES ('Ste Léocadie', 'LFYS', 'N422650', 'E0020039', 255);
INSERT INTO `icao` VALUES ('Sallanches', 'LFHZ', 'N455714', 'E0063821', 255);
INSERT INTO `icao` VALUES ('Salon Eyguières', 'LFNE', 'N433930', 'E0050049', 246);
INSERT INTO `icao` VALUES ('Sarlat Domme', 'LFDS', 'N444736', 'E0011441', 255);
INSERT INTO `icao` VALUES ('Sarre-Union', 'LFQU', 'N485705', 'E0070440', 255);
INSERT INTO `icao` VALUES ('Saumur St Florent', 'LFOD', 'N471524', 'W0000649', 255);
INSERT INTO `icao` VALUES ('Semur en Auxois', 'LFGQ', 'N472855', 'E0042039', 255);
INSERT INTO `icao` VALUES ('Serres la Batie Monsaléon', 'LFTM', 'N442730', 'E0054342', 255);
INSERT INTO `icao` VALUES ('Sisteron Thése', 'LFNS', 'N441715', 'E0055549', 255);
INSERT INTO `icao` VALUES ('Strasbourg Neuhof', 'LFGC', 'N483316', 'E0074641', 255);
INSERT INTO `icao` VALUES ('Tarbes Laloubère', 'LFDT', 'N431258', 'E0000443', 255);
INSERT INTO `icao` VALUES ('Tarbes Lourdes Pyrénées', 'LFBT', 'N431108', 'W0000010', 255);
INSERT INTO `icao` VALUES ('Thouars', 'LFCT', 'N465743', 'W0000910', 255);
INSERT INTO `icao` VALUES ('Toulouse Bourg St Bernard', 'LFIT', 'N433644', 'E0014331', 255);
INSERT INTO `icao` VALUES ('Tours Val de Loire', 'LFOT', 'N472555', 'E0004323', 255);
INSERT INTO `icao` VALUES ('Toussus le Noble', 'LFPN', 'N484459', 'E0020640', 255);
INSERT INTO `icao` VALUES ('Troyes Barberey', 'LFQB', 'N481918', 'E0040100', 255);
INSERT INTO `icao` VALUES ('Valence Chabeuil', 'LFLU', 'N445456', 'E0045807', 255);
INSERT INTO `icao` VALUES ('Vesoul Frotey', 'LFQW', 'N473822', 'E0061219', 255);
INSERT INTO `icao` VALUES ('Villefranche Tarare', 'LFHV', 'N455512', 'E0043806', 255);
INSERT INTO `icao` VALUES ('Saint André de l''Eure', 'LFFD', 'N485355', 'E0011502', 255);
INSERT INTO `icao` VALUES ('Saint Claude Pratz', 'LFKZ', 'N462320', 'E0054620', 255);
INSERT INTO `icao` VALUES ('Saint Jean d''Avelanne', 'LFKH', 'N453100', 'E0054050', 255);
INSERT INTO `icao` VALUES ('Saint Valéry Vittefleur', 'LFOS', 'N495010', 'E0003918', 255);
INSERT INTO `icao` VALUES ('La Roche sur Yon Les Ajoncs', 'LFRI', 'N464209', 'W0012254', 255);
INSERT INTO `icao` VALUES ('Bourges', 'LFLD', 'N470352', 'E0022244', 255);
INSERT INTO `icao` VALUES ('Lille Marcq en Baroeul', 'LFQO', 'N504114', 'E0030432', 70);
INSERT INTO `icao` VALUES ('Alès Déaux', 'LFMS', 'N440422', 'E0040835', 255);
INSERT INTO `icao` VALUES ('Lens Bénifontaine', 'LFQL', 'N502802', 'E0024919', 167);
INSERT INTO `icao` VALUES ('Valenciennes Denain', 'LFAV', 'N501929', 'E0032756', 177);
INSERT INTO `icao` VALUES ('Lille Lesquin', 'LFQQ', 'N503348', 'E0030513', 157);
INSERT INTO `icao` VALUES ('Merville Calonne', 'LFQT', 'N503700', 'E0023824', 61);
INSERT INTO `icao` VALUES ('Guiscriff Sca''r', 'LFES', 'N480317', 'W0033946', 255);
INSERT INTO `icao` VALUES ('Moulins Montbeugny', 'LFHY', 'N463204', 'E0032518', 255);
INSERT INTO `icao` VALUES ('Villeneuve sur Lot', 'LFCW', 'N442401', 'E0004540', 190);
INSERT INTO `icao` VALUES ('Amiens Glisy', 'LFAY', 'N495223', 'E0022313', 208);
INSERT INTO `icao` VALUES ('Le Luc le Cannet', 'LFMC', 'N432305', 'E0062313', 255);
INSERT INTO `icao` VALUES ('Auch Lamothe', 'LFDH', 'N434113', 'E0003600', 255);
INSERT INTO `icao` VALUES ('Cuers Pierrefeu', 'LFTF', 'N431451', 'E0060738', 255);
INSERT INTO `icao` VALUES ('Saintes Thénac', 'LFXB', 'N454207', 'W0003810', 118);
INSERT INTO `icao` VALUES ('Vannes Meucon', 'LFRV', 'N474309', 'W0024324', 255);
INSERT INTO `icao` VALUES ('Saint Yan', 'LFLN', 'N462423', 'E0040116', 255);
INSERT INTO `icao` VALUES ('Besançon la Vèze', 'LFQM', 'N471219', 'E0060450', 255);
INSERT INTO `icao` VALUES ('Beauvais Tillé', 'LFOB', 'N492716', 'E0020646', 255);
INSERT INTO `icao` VALUES ('Pontoise Cormeilles en Vexin', 'LFPT', 'N490548', 'E0020227', 255);
INSERT INTO `icao` VALUES ('Annemasse', 'LFLI', 'N461131', 'E0061610', 255);
INSERT INTO `icao` VALUES ('L''Alpe d''Huez', 'LFHU', 'N450518', 'E0060505', 255);
INSERT INTO `icao` VALUES ('Paris le Bourget', 'LFPB', 'N485810', 'E0022629', 218);
INSERT INTO `icao` VALUES ('Colmar Houssen', 'LFGA', 'N480637', 'E0072133', 255);
INSERT INTO `icao` VALUES ('Mimizan', 'LFCZ', 'N440849', 'W0010948', 164);
INSERT INTO `icao` VALUES ('Ile d'' Yeu le Grand Phare', 'LFEY', 'N464307', 'W0022328', 79);
INSERT INTO `icao` VALUES ('Lognes Emerainville', 'LFPL', 'N484923', 'E0023726', 255);
INSERT INTO `icao` VALUES ('Chalon Champforgeuil', 'LFLH', 'N464942', 'E0044901', 255);
INSERT INTO `icao` VALUES ('Megève', 'LFHM', 'N454915', 'E0063908', 255);
INSERT INTO `icao` VALUES ('Toulouse Blagnac', 'LFBO', 'N433806', 'E0012204', 255);
INSERT INTO `icao` VALUES ('Bastia Poretta', 'LFKB', 'N423300', 'E0092905', 26);
INSERT INTO `icao` VALUES ('Toulouse Lasbordes', 'LFCL', 'N433520', 'E0012959', 255);
INSERT INTO `icao` VALUES ('Valréas Visan', 'LFNV', 'N442013', 'E0045429', 255);
INSERT INTO `icao` VALUES ('Bordeaux Léognan Saucats', 'LFCS', 'N444201', 'W0003544', 190);
INSERT INTO `icao` VALUES ('Vinon sur Verdon', 'LFNF', 'N434416', 'E0054703', 255);
INSERT INTO `icao` VALUES ('Villacoublay Vélizy', 'LFPV', 'N484627', 'E0021130', 255);
INSERT INTO `icao` VALUES ('Lanvéoc Poulmic', 'LFRL', 'N481657', 'W0042637', 255);
INSERT INTO `icao` VALUES ('Saint Dizier Robinson', 'LFSI', 'N483800', 'E0045429', 255);
INSERT INTO `icao` VALUES ('Toul Rosières', 'LFSL', 'N484648', 'E0055848', 255);
INSERT INTO `icao` VALUES ('Avord', 'LFOA', 'N470325', 'E0023820', 255);
INSERT INTO `icao` VALUES ('Brétigny sur Orge', 'LFPY', 'N483546', 'E0021956', 255);
INSERT INTO `icao` VALUES ('Mourmelon', 'LFXM', 'N490641', 'E0042201', 255);
INSERT INTO `icao` VALUES ('Orange Caritat', 'LFMO', 'N440824', 'E0045206', 197);
INSERT INTO `icao` VALUES ('Colmar Meyenheim', 'LFSC', 'N475519', 'E0072358', 255);
INSERT INTO `icao` VALUES ('Évreux Fauville', 'LFOE', 'N490143', 'E0011312', 255);
INSERT INTO `icao` VALUES ('Luxeuil St Sauveur', 'LFSX', 'N474714', 'E0062154', 255);
INSERT INTO `icao` VALUES ('Nancy Ochey', 'LFSO', 'N483459', 'E0055716', 255);
INSERT INTO `icao` VALUES ('Coetquidan', 'LFXQ', 'N475638', 'W0021055', 255);
INSERT INTO `icao` VALUES ('Damblain', 'LFYD', 'N480510', 'E0053949', 255);
INSERT INTO `icao` VALUES ('Solenzara', 'LFKS', 'N415534', 'E0092419', 28);
INSERT INTO `icao` VALUES ('Cessey Baigneux les Juifs', 'LFSY', 'N473635', 'E0043703', 0);
INSERT INTO `icao` VALUES ('Landivisiau', 'LFRJ', 'N483148', 'W0040905', 255);
INSERT INTO `icao` VALUES ('Mont de Marsan', 'LFBM', 'N435443', 'W0002924', 203);
INSERT INTO `icao` VALUES ('Étain Rouvres', 'LFQE', 'N491344', 'E0054033', 255);
INSERT INTO `icao` VALUES ('Cambrai Epinoy', 'LFQI', 'N501309', 'E0030908', 255);
INSERT INTO `icao` VALUES ('Toulouse Francazal', 'LFBF', 'N433256', 'E0012126', 255);
INSERT INTO `icao` VALUES ('Cazaux', 'LFBC', 'N443205', 'W0010753', 84);
INSERT INTO `icao` VALUES ('Châteaudun', 'LFOC', 'N480328', 'E0012245', 255);

-- --------------------------------------------------------

-- 
-- Structure de la table `instructors`
-- 

CREATE TABLE `instructors` (
  `INST_NUM` int(10) unsigned NOT NULL default '0',
  `SIGN` varchar(255) NOT NULL default '',
  `TYPES_INST` text NOT NULL,
  `FE` tinyint(3) unsigned NOT NULL default '0',
  `IR` tinyint(3) unsigned NOT NULL default '0',
  `AEROBATIC` tinyint(3) unsigned NOT NULL default '0',
  `NIGHT_FLIGHT` tinyint(3) unsigned NOT NULL default '0',
  `ORDER_NUM` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`INST_NUM`),
  UNIQUE KEY `ORDER_NUM` (`ORDER_NUM`)
) TYPE=MyISAM COMMENT='LIST INSTRUCTORS AND THEIR AUTORIZATIONS';

-- 
-- Contenu de la table `instructors`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `ip_stopped`
-- 

CREATE TABLE `ip_stopped` (
  `IP_NUM` varchar(255) NOT NULL default '',
  `COUNTER` tinyint(3) unsigned NOT NULL default '0',
  `EXPIRE_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`IP_NUM`)
) TYPE=MyISAM COMMENT='BLACKLISTED IP';

-- 
-- Contenu de la table `ip_stopped`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `login_stopped`
-- 

CREATE TABLE `login_stopped` (
  `LOGIN` varchar(255) NOT NULL default '',
  `COUNTER` tinyint(3) unsigned NOT NULL default '0',
  `EXPIRE_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`LOGIN`)
) TYPE=MyISAM COMMENT='BLACKLISTED LOGIN';

-- 
-- Contenu de la table `login_stopped`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `logs`
-- 

CREATE TABLE `logs` (
  `DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `USER` int(10) unsigned NOT NULL default '0',
  `MESSAGE` text NOT NULL
) TYPE=MyISAM COMMENT='TRACE EVERY ACTION PERFORMED ON THE BASE';

-- 
-- Contenu de la table `logs`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `member_qualif`
-- 

CREATE TABLE `member_qualif` (
  `MEMBERNUM` int(10) unsigned NOT NULL default '0',
  `QUALIFID` int(10) unsigned NOT NULL default '0',
  `EXPIREDATE` date NOT NULL default '0000-00-00',
  `NOALERT` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`MEMBERNUM`,`QUALIFID`)
) TYPE=MyISAM COMMENT='QUALIFICATIONS OF EACH MEMBER';

-- 
-- Contenu de la table `member_qualif`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `members`
-- 

CREATE TABLE `members` (
  `NUM` int(10) unsigned NOT NULL default '0',
  `MEMBER_NUM` varchar(255) NOT NULL default '',
  `SUBSCRIPTION` date NOT NULL default '2004-12-31',
  `QUALIF_ALERT_DELAY` tinyint(3) unsigned NOT NULL default '8',
  PRIMARY KEY  (`NUM`)
) TYPE=MyISAM COMMENT='ALL MEMBERS OF ALL AIRCLUBS';

-- 
-- Contenu de la table `members`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `parameter`
-- 

CREATE TABLE `parameter` (
  `CODE` varchar(255) NOT NULL default '',
  `ENABLED` tinyint(3) unsigned NOT NULL default '0',
  `INT_VALUE` int(10) default NULL,
  `CHAR_VALUE` varchar(255) default NULL,
  PRIMARY KEY  (`CODE`)
) TYPE=MyISAM COMMENT='APPLICATION PARAMETERS';

-- 
-- Contenu de la table `parameter`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `profiles`
-- 

CREATE TABLE `profiles` (
  `NUM` mediumint(8) unsigned NOT NULL default '0',
  `NAME` varchar(255) NOT NULL default '',
  `PERMITS` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NUM`)
) TYPE=MyISAM;

-- 
-- Contenu de la table `profiles`
-- 

INSERT INTO `profiles` VALUES (1, 'club admin', 832);

-- --------------------------------------------------------

-- 
-- Structure de la table `qualification`
-- 

CREATE TABLE `qualification` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NAME` varchar(255) NOT NULL default '',
  `TIME_LIMITATION` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM COMMENT='QUALIFICATIONS LIST' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `qualification`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `regular_presence_inst_dates`
-- 

CREATE TABLE `regular_presence_inst_dates` (
  `INST_NUM` int(10) unsigned NOT NULL default '0',
  `START_DAY` tinyint(3) unsigned NOT NULL default '0',
  `END_DAY` tinyint(3) unsigned NOT NULL default '0',
  `START_HOUR` time NOT NULL default '00:00:00',
  `END_HOUR` time NOT NULL default '00:00:00'
) TYPE=MyISAM;
