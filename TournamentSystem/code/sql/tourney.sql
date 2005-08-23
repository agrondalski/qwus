-- TODO
-- 1. unique player names ?

-- DONE
-- 1. not null constraints
-- 2. unique constraints
-- 3. removed phpbb dependence
-- 4. added tourney_playoffs
-- 5. added canPostNews to tourney_admins
-- 6. added SuperAdmin to player
-- 7. various additional columns

drop database if exists dew_test  ;
create database if not exists dew_test ;
use dew_test ;

create table game_type(
  game_type_id  integer       NOT NULL auto_increment,
  name          varchar(250)  NOT NULL,
--
  constraint game_type_pk   primary key(game_type_id),
  constraint game_type_unq1 unique(name)) 
ENGINE=INNODB;

create table tourney(
  tourney_id         integer       NOT NULL auto_increment,
  game_type_id       integer       NOT NULL,
  name               varchar(250)  NOT NULL,
  tourney_type       ENUM ('LADDER', 'LEAGUE', 'TOURNAMENT') NOT NULL default 'LEAGUE',
  signup_start       date          NOT NULL,
  signup_end         date          NOT NULL,
  team_size          integer       NOT NULL,  -- 4v4, 1v1, etc
  timelimit          integer       NOT NULL,
--
  constraint tournament_pk   primary key(tourney_id),
  constraint tournament_unq1 unique(name))
ENGINE=INNODB ;

create table tourney_admins(
  tourney_id   integer  NOT NULL,
  admin_id     integer  NOT NULL,
  canPostNews  boolean  NOT NULL default false,
--
  constraint tourney_admins_fk primary key(tourney_id, admin_id))
ENGINE=INNODB ;

create table tourney_maps(
  tourney_maps_id  integer  NOT NULL auto_increment,
  tourney_id       integer  NOT NULL,
  map_id           integer  NOT NULL,
--
  constraint tourney_maps_pk primary key(tourney_maps_id))
ENGINE=INNODB ;

create table tourney_playoffs(
  tourney_id         integer NOT NULL,
  division_id        integer NOT NULL,
  num_playoff_spots  integer NOT NULL,
  elim_losses        integer NOT NULL,
--
  constraint tourney_playoffs_pk primary key(tourney_id, division_id))
ENGINE=INNODB ;

create table tourney_schedule(
  tourney_schedule_id  integer      NOT NULL auto_increment,
  tourney_id           integer      NOT NULL,
  division_id          integer      NOT NULL,
  name                 varchar(250) NOT NULL,  -- Week1, Game4, Quarterfinals
  deadline             date         NOT NULL,  -- deadline for reporting
--
  constraint tourney_schedule_pk primary key(tourney_schedule_id))
ENGINE=INNODB ;

create table division(
  division_id   integer      NOT NULL auto_increment,
  tourney_id    integer      NOT NULL,
  name          varchar(250) NOT NULL,
  max_teams     integer,     -- null or -1 if no max
  num_games     integer      NOT NULL,
--
  constraint   division_pk primary key(division_id))
ENGINE=INNODB ;

create table team(
  team_id      integer      NOT NULL auto_increment,
  name         varchar(250) NOT NULL,
  email        varchar(250) NOT NULL,
  irc_channel  varchar(250),
  location_id  integer      NOT NULL,
  password     varchar(250) NOT NULL,
--
  constraint team_pk   primary key(team_id),
  constraint team_unq1 unique(name))
ENGINE=INNODB ;

create table tourney_info(
  tourney_id   integer NOT NULL,
  team_id      integer NOT NULL,
  division_id  integer NOT NULL,
  wins         integer NOT NULL default 0,
  losses       integer NOT NULL default 0,
  points       integer NOT NULL default 0,
  maps_won     integer NOT NULL default 0,
  maps_lost    integer NOT NULL default 0,
--
  constraint tourney_participants_pk primary key(tourney_id, team_id))
ENGINE=INNODB ;

create table player(
  player_id    integer       NOT NULL auto_increment,
  name         varchar(250)  NOT NULL,
  superAdmin   boolean       NOT NULL default false,
  location_id  integer       NOT NULL,
  password     varchar(250), -- not null for admins
--
  constraint player_pk   primary key(player_id))
--  constraint player_unq1 unique(name))
ENGINE=INNODB ;

-- Each country has an entry with a null state_name, used as default for a particular country
create table location(
  location_id   integer      NOT NULL auto_increment,
  country_name  varchar(250) NOT NULL,
  state_name    varchar(250),
  logo_url      varchar(250) NOT NULL,
--
  constraint location_pk primary key(location_id))
ENGINE=INNODB ;

create table player_info(
  tourney_id  integer NOT NULL,
  team_id     integer NOT NULL,
  player_id   integer NOT NULL,
  isAdmin     boolean NOT NULL default FALSE,
--
  constraint player_lookup_pk primary key(tourney_id, team_id, player_id))
ENGINE=INNODB ;

-- This table also stores the schedule with winning_team_id/match_date/etc. being null.
create table match_table(
  match_id             integer NOT NULL auto_increment,
  tourney_schedule_id  integer NOT NULL,
  team1_id             integer NOT NULL,
  team2_id             integer NOT NULL,
  winning_team_id      integer,
  approved             boolean NOT NULL default FALSE,
  match_date           date,
--
  constraint match_pk primary key(match_id))
ENGINE=INNODB ;

create table game(
  game_id         integer NOT NULL auto_increment,
  match_id        integer NOT NULL,
  map_id          integer NOT NULL,
  team1_score     integer NOT NULL,
  team2_score     integer NOT NULL,
  screenshot_url  varchar(250),
  demo_url        varchar(250),
--
  constraint game2_pk primary key(game_id))
ENGINE=INNODB ;

create table maps(
  map_id        integer      NOT NULL auto_increment,
  map_name      varchar(250) NOT NULL,
  map_abbr      varchar(10)  NOT NULL,
  game_type_id  integer      NOT NULL,
--
  constraint maps_pk primary key(map_id))
ENGINE=INNODB ;

create table stats(
  player_id  integer      NOT NULL,
  game_id    integer      NOT NULL,
  score      varchar(250) NOT NULL,
  time       integer      NOT NULL,  -- needed ?  should also be tourney timelimit, if a player dropped we really wont know actual time anyways
--
  constraint stats_pk primary key(player_id, game_id))
ENGINE=INNODB ;

create table comments(
  comments_id   integer       NOT NULL auto_increment,
  name          integer       NOT NULL,
  player_ip     varchar(250)  NOT NULL,
  match_id      integer       NOT NULL,
  comment_text  MEDIUMTEXT    NOT NULL,
  comment_date  date          NOT NULL, -- needed for sorting
  comment_time  time          NOT NULL,
--
  constraint comments_pk primary key(comments_id))
ENGINE=INNODB ;

-- Need a way to select tourney when posting news. Needs permission from tourney_admins table or SuperAdmin from player_table.
create table news(
  news_id       bigint      NOT NULL auto_increment, 
  writer_id     integer     NOT NULL,
  tourney_id    integer,
  subject       text        NOT NULL,
  news_date     date        NOT NULL,
  text          MEDIUMTEXT  NOT NULL,
--
  constraint news_pk primary key(news_id))
ENGINE=INNODB ;


-- Add RIC constraints
alter table tourney add constraint tourney_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table tourney_admins add constraint tourney_admins_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_admins add constraint tourney_admins_fk2 foreign key(admin_id) references player(player_id) ;

alter table tourney_maps add constraint tourney_maps_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_maps add constraint tourney_maps_fk2 foreign key(map_id) references maps(map_id) ;

alter table tourney_playoffs add constraint tourney_playoffs_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_playoffs add constraint tourney_playoffs_fk2 foreign key(division_id) references division(division_id) ;

alter table tourney_schedule add constraint tourney_schedule_fk1 foreign key(tourney_id)  references tourney(tourney_id) ;
alter table tourney_schedule add constraint tourney_schedule_fk2 foreign key(division_id) references division(division_id) ;

alter table division add constraint division_fk1 foreign key(tourney_id) references tourney(tourney_id) ;

alter table team add constraint team_fk1 foreign key(location_id) references location(location_id) ;

alter table tourney_info add constraint tourney_info_fk1 foreign key(tourney_id)  references tourney(tourney_id) ; 
alter table tourney_info add constraint tourney_info_fk2 foreign key(division_id) references division(division_id) ;
alter table tourney_info add constraint tourney_info_fk3 foreign key(team_id)     references team(team_id) ; 

alter table player add constraint player_fk2 foreign key(location_id) references location(location_id) ;

alter table player_info add constraint player_lookup_fk3 foreign key(tourney_id) references tourney(tourney_id) ;
alter table player_info add constraint player_lookup_fk2 foreign key(team_id)    references team(team_id) ;
alter table player_info add constraint player_lookup_fk1 foreign key(player_id)  references player(player_id) ;

alter table match_table add constraint match_fk1 foreign key(tourney_schedule_id)  references tourney_schedule(tourney_schedule_id) ;
alter table match_table add constraint match_fk2 foreign key(team1_id)    references team(team_id) ;
alter table match_table add constraint match_fk3 foreign key(team2_id)    references team(team_id) ;
alter table match_table add constraint match_fk4 foreign key(winning_team_id) references team(team_id) ;

alter table game add constraint game_fk1 foreign key(match_id) references match_table(match_id) ;
alter table game add constraint game_fk2 foreign key(map_id)   references tourney_maps(tourney_maps_id) ;

alter table maps add constraint maps_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table stats add constraint stats_fk1 foreign key(player_id) references player(player_id) ;
alter table stats add constraint stats_fk2 foreign key(game_id)   references game(game_id) ;

alter table comments add constraint comments_fk2 foreign key(match_id)  references match_table(match_id) ;

alter table news add constraint news_fk1 foreign key(writer_id) references player(player_id) ;
alter table news add constraint news_fk2 foreign key(tourney_id) references tourney(tourney_id) ;
