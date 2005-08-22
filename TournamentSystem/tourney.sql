-- TODO
-- ????

-- Cleanup up before continuing
drop table stats ;
drop table comments ;
drop table game ;
drop table match_table ;
drop table player_info ;
drop table tourney_maps ;
drop table tourney_info ;
drop table division ;
drop table tourney_admins ;
drop table tourney_schedule ;
drop table tourney ;
drop table team ;
drop table player ;
drop table country ;
drop table maps ;
drop table game_type ;


create table game_type(
  game_type_id  integer auto_increment,
  name          varchar(250),
  constraint game_type_pk primary key(game_type_id)) 
ENGINE=INNODB;

create table tourney(
  tourney_id         integer auto_increment,
  game_type_id       integer,
  name               varchar(250),
  isLadder           boolean,
  signup_start       date,
  signup_end         date,
  team_size          integer,  -- 4v4, 1v1, etc
  constraint tournament_pk primary key(tourney_id))
ENGINE=INNODB ;

create table tourney_admins(
  tourney_id  integer,
  admin_id    integer,
  constraint tourney_admins_fk primary key(tourney_id, admin_id))
ENGINE=INNODB ;

create table tourney_maps(
  tourney_id  integer,
  map_id      integer,
  constraint tourney_maps_pk primary key(tourney_id, map_id))
ENGINE=INNODB ;

create table tourney_schedule(
  tourney_schedule_id  integer auto_increment,
  tourney_id           integer,
  name                 varchar(250),  -- Week1, Game4, Quarterfinals
  deadline             date,          -- deadline for reporting
  constraint tourney_schedule_pk primary key(tourney_schedule_id))
ENGINE=INNODB ;

create table division(
  division_id        integer auto_increment,
  tourney_id         integer,
  name               varchar(250),
  max_teams          integer,  -- null or -1 if no max
  num_games          integer,
  num_playoff_spots  integer,  -- can be used to derive number of playoff rounds and any potential byes
  constraint   division_pk primary key(division_id))
ENGINE=INNODB ;

create table team(
  team_id      integer auto_increment,
  name         varchar(250),
  email        varchar(250),
  irc_channel  varchar(250),
  country_id   integer,
  password     varchar(250),
  constraint team_pk PRIMARY KEY(team_id))
ENGINE=INNODB ;

create table tourney_info(
  tourney_id   integer,
  team_id      integer,
  division_id  integer,
  wins         integer,
  losses       integer,
  points       integer,
  maps_won     integer,
  maps_lost    integer,
  constraint tourney_participants_pk primary key(tourney_id, team_id))
ENGINE=INNODB ;

-- Commenting out columns in favor of phpbb.user
-- RIC cannot currently be enforced due to phpbb.phpbb_users being a MYISAM table
-- Need to determine how players can be added (thru phpbb only, or both) as it affect a number of things.
create table player(
  player_id   integer auto_increment,
--name        varchar(250),
--email       varchar(250),
--country_id  integer,
--password    varchar(250),
  constraint player_pk primary key(player_id))
ENGINE=INNODB ;

create table country(
  country_id  integer auto_increment,
  name        integer,
  logo_url    varchar(250),
  constraint country_pk primary key(country_id))
ENGINE=INNODB ;

create table player_info(
  tourney_id  integer,
  team_id     integer,
  player_id   integer,
  isAdmin     boolean,
  constraint player_lookup_pk primary key(tourney_id, team_id, player_id))
ENGINE=INNODB ;

-- This table also stores the schedule with winning_team_id/match_date/etc. being null.
create table match_table(
  match_id         integer auto_increment,
  tourney_schedule_id integer,
  team1_id         integer,
  team2_id         integer,
  winning_team_id  integer,
  approved         boolean,
  match_date       date,
  constraint match_pk primary key(match_id))
ENGINE=INNODB ;

create table game(
  game_id         integer auto_increment,
  match_id        integer,
  map_id          integer,
  team1_score     integer,
  team2_score     integer,
  screenshot_url  varchar(250),
  demo_url        varchar(250),
  constraint game2_pk primary key(game_id))
ENGINE=INNODB ;

create table maps(
  map_id        integer auto_increment,
  map_name      varchar(250),
  map_abbr      varchar(10),
  game_type_id  integer,
  constraint maps_pk primary key(map_id))
ENGINE=INNODB ;

create table stats(
  player_id  integer,
  game_id    integer,
  score      varchar(250),
  time       integer,
  constraint stats_pk primary key(player_id, game_id))
ENGINE=INNODB ;

create table comments(
  comments_id   integer auto_increment,
  player_id     integer,
  match_id      integer,
  comment       MEDIUMTEXT,
  comment_date  date,  -- needed for sorting
  constraint comments_pk primary key(comments_id))
ENGINE=INNODB ;

alter table tourney add constraint tourney_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table tourney_admins add constraint tourney_admins_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_admins add constraint tourney_admins_fk2 foreign key(admin_id) references player(player_id) ;

alter table tourney_maps add constraint tourney_maps_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_maps add constraint tourney_maps_fk2 foreign key(map_id) references maps(map_id) ;

alter table tourney_schedule add constraint tourney_schedule_fk1 foreign key(tourney_id) references tourney(tourney_id) ;

alter table division add constraint division_fk1 foreign key(tourney_id) references tourney(tourney_id) ;

alter table team add constraint team_fk1 foreign key(country_id) references country(country_id) ;

alter table tourney_info add constraint tourney_info_fk1 foreign key(tourney_id)  references tourney(tourney_id) ; 
alter table tourney_info add constraint tourney_info_fk2 foreign key(division_id) references division(division_id) ;
alter table tourney_info add constraint tourney_info_fk3 foreign key(team_id)     references team(team_id) ; 

--alter table player add constraint player_fk1 foreign key(player_id)  references phpbb.phpbb_users ;
--alter table player add constraint player_fk2 foreign key(country_id) references country(country_id) ;

alter table player_info add constraint player_lookup_fk3 foreign key(tourney_id) references tourney(tourney_id) ;
alter table player_info add constraint player_lookup_fk2 foreign key(team_id)    references team(team_id) ;
alter table player_info add constraint player_lookup_fk1 foreign key(player_id)  references player(player_id) ;

alter table match_table add constraint match_fk1 foreign key(tourney_schedule_id)  references tourney_schedule(tourney_schedule_id) ;
alter table match_table add constraint match_fk2 foreign key(team1_id)    references team(team_id) ;
alter table match_table add constraint match_fk3 foreign key(team2_id)    references team(team_id) ;
alter table match_table add constraint match_fk4 foreign key(winning_team_id) references team(team_id) ;

alter table game add constraint game_fk1 foreign key(match_id) references match_table(match_id) ;
alter table game add constraint game_fk2 foreign key(map_id)   references tourney_maps(map_id) ;

alter table maps add constraint maps_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table stats add constraint stats_fk1 foreign key(player_id) references player(player_id) ;
alter table stats add constraint stats_fk2 foreign key(game_id)   references game(game_id) ;

alter table comments add constraint comments_fk1 foreign key(player_id) references player(player_id) ;
alter table comments add constraint comments_fk2 foreign key(match_id)  references match_table(match_id) ;
