-- clanbase.com
-- pts / wins / loses per team
-- deadline for tournies
-- ranks for ladders
-- team password

drop table stats ;
drop table comments ;
drop table tourney_admins ;
drop table game ;
drop table maps ;
drop table match_table ;
drop table player_lookup ;
drop table player ;
drop table team ;
drop table division ;
drop table tourney ;
drop table tourney_type ;

create table team(
  team_id      integer auto_increment,
  name         varchar(250),
  email        varchar(250),
  irc_channel  varchar(250),
  constraint team_pk PRIMARY KEY(team_id))
ENGINE=INNODB ;

-- useful ?? or just keep it up there
create table tourney_participants(
  tourney_id  integer,
  team_id     integer,
  constraint tourney_participants_pk primary key(tourney_id, part_id))
ENGINE=INNODB ;

create table player(
  player_id  integer auto_increment,
  name       varchar(250),
  email      varchar(250),
  password   varchar(250),
  constraint player_pk primary key(player_id))
ENGINE=INNODB ;

create table player_lookup(
  tourney_id  integer,
  team_id     integer,
  player_id   integer,
  isAdmin     boolean,
  constraint player_lookup_pk primary key(tourney_id, team_id, player_id))
ENGINE=INNODB ;

create table tourney(
  tourney_id       integer auto_increment,
  tourney_type_id  integer,
  name             varchar(250),
  constraint tournament_pk primary key(tourney_id))
ENGINE=INNODB ;

create table tourney_type(
  tourney_type_id  integer auto_increment,
  name             varchar(250),
  constraint tourney_type_pk primary key(tourney_type_id))
ENGINE=INNODB ;

create table tourney_admins(
  player_id   integer,
  tourney_id  integer,
  constraint tourney_admins_pk primary key(player_id, tourney_id))
ENGINE=INNODB ;

create table match_table(
  match_id         integer auto_increment,
  tourney_id       integer,
  team1_id         integer,
  team2_id         integer,
  winning_team_id  integer,
  approved         boolean,
  match_date       date,
  constraint match_pk primary key(match_id))
ENGINE=INNODB ;

create table maps(
  map_id    integer auto_increment,
  map_name  varchar(250),
  constraint maps_pk primary key(map_id))
ENGINE=INNODB ;

create table comments(
  comments_id  integer auto_increment,
  player_id    integer,
  match_id     integer,
  comment      TEXT,
  constraint comments_pk primary key(comments_id))
ENGINE=INNODB ;

create table stats(
  player_id  integer,
  game_id    integer,
  score      varchar(250),
  time       integer,
  constraint stats_pk primary key(player_id, game_id))
ENGINE=INNODB ;

create table division(
  division_id  integer,
  tourney_id   integer,
  name         varchar(250),
  constraint   division_pk primary key(division_id))
ENGINE=INNODB ;

create table game(
  game_id      integer auto_increment,
  match_id     integer,
  map_id       integer,
  team1_score  integer,
  team2_score  integer,
  screenshot   varchar(250),  -- assuming url, not file
  demo         varchar(250),  -- same
  constraint game2_pk primary key(game_id))
ENGINE=INNODB ;

alter table tourney_participants add constraint tourney_participants_fk1 foreign key(tourney_id) references tourney(tourney_id) ; 

alter table tourney add constraint foreign key(tourney_type_id) references tourney_type(tourney_type_id) ;

alter table player_lookup add constraint player_lookup_fk3 foreign key(tourney_id) references tourney(tourney_id) ;
alter table player_lookup add constraint player_lookup_fk2 foreign key(team_id)    references team(team_id) ;
alter table player_lookup add constraint player_lookup_fk1 foreign key(player_id)  references player(player_id) ;

alter table match_table add constraint match_fk1 foreign key(tourney_id)  references tourney(tourney_id) ;
alter table match_table add constraint match_fk2 foreign key(team1_id)    references team(team_id) ;
alter table match_table add constraint match_fk3 foreign key(team2_id)    references team(team_id) ;
alter table match_table add constraint match_fk4 foreign key(winning_team_id) references team(team_id) ;

alter table tourney_admins add constraint toruney_admins_fk1 foreign key(player_id)  references player(player_id) ;
alter table tourney_admins add constraint toruney_admins_fk2 foreign key(tourney_id) references tourney(tourney_id) ;

alter table game add constraint game_fk1 foreign key(match_id) references match_table(match_id) ;
alter table game add constraint game_fk2 foreign key(map_id)   references maps(map_id) ;

alter table stats add constraint stats_fk1 foreign key(player_id) references player(player_id) ;
alter table stats add constraint stats_fk2 foreign key(game_id) references game(game_id) ;

alter table comments add constraint comments_fk1 foreign key(player_id) references player(player_id) ;
alter table comments add constraint comments_fk2 foreign key(match_id)  references match_table(match_id) ;

alter table division add constraint division_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
