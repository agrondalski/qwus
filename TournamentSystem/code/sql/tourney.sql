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
  rules              MEDIUMTEXT,
  tourney_type       ENUM ('Tournament', 'League', 'Ladder') NOT NULL default 'LEAGUE',
  status             ENUM ('Signups', 'Regular Season', 'Playoffs', 'Complete') NOT NULL default 'Signups',
  team_size          integer       NOT NULL,
  timelimit          integer       NOT NULL,
--
  constraint tournament_pk   primary key(tourney_id),
  constraint tournament_unq1 unique(name))
ENGINE=INNODB ;

create table tourney_admins(
  tourney_id   integer  NOT NULL,
  player_id    integer  NOT NULL,
--
  constraint tourney_admins_fk primary key(tourney_id, player_id))
ENGINE=INNODB ;

create table tourney_maps(
--tourney_maps_id  integer  NOT NULL auto_increment,
  tourney_id       integer  NOT NULL,
  map_id           integer  NOT NULL,
--
  constraint tourney_maps_pk primary key(tourney_id, map_id))
ENGINE=INNODB ;

--create table tourney_playoffs(
--  tourney_id         integer NOT NULL,
--  division_id        integer NOT NULL,
--  num_playoff_spots  integer NOT NULL,
--  elim_losses        integer NOT NULL,
--
--constraint tourney_playoffs_pk primary key(tourney_id, division_id))
--ENGINE=INNODB ;

create table division(
  division_id   integer      NOT NULL auto_increment,
  tourney_id    integer      NOT NULL,
  name          varchar(250) NOT NULL,
  num_games     integer      NOT NULL,
  playoff_spots integer      NOT NULL,
  elim_losses   integer      NOT NULL,
--
  constraint   division_pk primary key(division_id))
ENGINE=INNODB ;

create table team(
  team_id      integer      NOT NULL auto_increment,
  name         varchar(250) NOT NULL,
  name_abbr    varchar(10)  NOT NULL,
  email        varchar(250) NOT NULL,
  irc_channel  varchar(250),
  location_id  integer      NOT NULL,
  password     varchar(250) NOT NULL,
  approved     boolean      NOT NULL default FALSE,
--
  constraint team_pk   primary key(team_id),
  constraint team_unq1 unique(name))
ENGINE=INNODB ;

create table tourney_info(
  tourney_id   integer NOT NULL,
  team_id      integer NOT NULL,
  division_id  integer,

  constraint tourney_participants_pk primary key(tourney_id, team_id))
ENGINE=INNODB ;

create table player(
  player_id    integer       NOT NULL auto_increment,
  name         varchar(250)  NOT NULL,
  superAdmin   boolean       NOT NULL default false,
  location_id  integer       NOT NULL,
  password     varchar(250),
  hasColumn    boolean       NOT NULL default false,
--
  constraint player_pk   primary key(player_id),
  constraint player_unq1 unique(name))
ENGINE=INNODB ;

create table location(
  location_id   integer      NOT NULL auto_increment,
  country_name  varchar(250) NOT NULL,
--  state_name    varchar(250),
  logo_url      varchar(250) NOT NULL,
--
  constraint location_pk primary key(location_id),
  constraint location_unq1 UNIQUE(country_name))
ENGINE=INNODB ;

create table player_info(
  tourney_id    integer NOT NULL,
  player_id     integer NOT NULL,
  team_id       integer NOT NULL,
  isTeamLeader  boolean NOT NULL default FALSE,
--
  constraint player_lookup_pk primary key(tourney_id, player_id))
ENGINE=INNODB ;

create table match_table(
  match_id             integer NOT NULL auto_increment,
  schedule_id          integer NOT NULL,
  team1_id             integer NOT NULL,
  team2_id             integer NOT NULL,
  winning_team_id      integer,
  approved             boolean NOT NULL default FALSE,
  match_date           date,
--
  constraint match_pk primary key(match_id))
ENGINE=INNODB ;
--create index match_idx1 on match_table(schedule_id) ;

create table match_schedule(
  schedule_id          integer      NOT NULL auto_increment,
  division_id          integer      NOT NULL,
  name                 varchar(250) NOT NULL,
  deadline             date,
--
  constraint match_schedule_pk primary key(schedule_id))
ENGINE=INNODB ;
--create index match_schedule_idx1 on match_schedule(division_id) ;

create table game(
  game_id         integer NOT NULL auto_increment,
  match_id        integer NOT NULL,
  map_id          integer NOT NULL,
  team1_score     integer NOT NULL,
  team2_score     integer NOT NULL,
--
  constraint game_pk primary key(game_id))
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
  stat_id    integer       NOT NULL auto_increment,
  player_id  integer       NOT NULL,
  game_id    integer       NOT NULL,
  team_id    integer       NOT NULL,
  stat_name  varchar(250)  NOT NULL,
  value      integer       NOT NULL,
--
  constraint stats_pk primary key(stat_id))
ENGINE=INNODB ;
--create index stats_idx1 on stats(game_id) ;
--create index stats_idx2 on stats(stat_name) ;
drop index stats_idx on stats ;
create index stats_idx on stats_new(player_id, game_id) ;


create table stats_team(
  team_id    integer       NOT NULL,
  game_id    integer       NOT NULL,
  stat_name  varchar(250)  NOT NULL,
  value      integer       NOT NULL,
--
  constraint stats_team_pk primary key(team_id, game_id, stat_name))
ENGINE=INNODB ;
--create index stats_team_idx1 on stats_team(game_id) ;
--create index stats_team_idx2 on stats_team(stat_name) ;

create table comments(
  comment_id    integer       NOT NULL auto_increment,
  comment_type  ENUM('Match', 'News', 'Column') NOT NULL default 'NEWS', 
  id            integer       NOT NULL,
  name          varchar(250)  NOT NULL,
  comment_ip    varchar(250)  NOT NULL,
  comment_text  TEXT          NOT NULL,
  comment_date  date          NOT NULL,
  comment_time  time          NOT NULL,
--
  constraint comments_pk primary key(comment_id))
ENGINE=INNODB ;

create table news(
  news_id       bigint      NOT NULL auto_increment, 
  writer_id     integer     NOT NULL,
  news_type     ENUM('News', 'Tournament', 'Column') NOT NULL default 'NEWS', 
  id            integer,
  subject       TEXT        NOT NULL,
  news_date     date        NOT NULL,
  text          MEDIUMTEXT  NOT NULL,
--
  constraint news_pk primary key(news_id))
ENGINE=INNODB ;

create table log_table(
  log_id      bigint       NOT NULL auto_increment,
  type        varchar(250) NOT NULL,
  str         varchar(250) NOT NULL,
  logged_ip   varchar(250) NOT NULL,
  log_date    date         NOT NULL,
  log_time    time         NOT NULL,
  constraint log_table_pk primary key(log_id))
ENGINE=INNODB ;

create table poll( 
  poll_id     integer       NOT NULL auto_increment, 
  topic       varchar(250)  NOT NULL,
  poll_type   ENUM('Match', 'News', 'Tournament') NOT NULL default 'MATCH', 
  id          integer       NOT NULL, 
  isCurrent   boolean       NOT NULL default FALSE,
--
  constraint poll_pk primary key(poll_id))
ENGINE=INNODB ;
  
create table poll_options( 
  poll_id      integer      NOT NULL, 
  option_id    integer      NOT NULL,
  poll_option  varchar(250) NOT NULL, 
  votes        integer      NOT NULL default 0, 
--
  constraint poll_options_pk primary key(poll_id, poll_option))
ENGINE=INNODB ;

create table poll_votes(
  poll_id  integer     NOT NULL,
  vote_ip  varchar(25) NOT NULL,
--
  constraint poll_votes_pk primary key(poll_id, vote_ip))
ENGINE=INNODB ;

create table file_table(
  file_id    integer       NOT NULL auto_increment,
  file_type  ENUM('Game', 'Match') NOT NULL default 'Game',
  id         integer       NOT NULL,
  file_desc  varchar(250)  NOT NULL,
  url        varchar(250)  NOT NULL,
--
  constraint file_table_pk primary key(file_id),
  constraint file_table_unq1 unique(id, file_type, file_desc))
ENGINE=INNODB ;

-- Add RIC constraints
alter table tourney add constraint tourney_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table tourney_admins add constraint tourney_admins_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_admins add constraint tourney_admins_fk2 foreign key(player_id) references player(player_id) ;

alter table tourney_maps add constraint tourney_maps_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
alter table tourney_maps add constraint tourney_maps_fk2 foreign key(map_id) references maps(map_id) ;

--alter table tourney_playoffs add constraint tourney_playoffs_fk1 foreign key(tourney_id) references tourney(tourney_id) ;
--alter table tourney_playoffs add constraint tourney_playoffs_fk2 foreign key(division_id) references division(division_id) ;

alter table match_schedule add constraint match_schedule_fk1 foreign key(division_id) references division(division_id) ;

alter table division add constraint division_fk1 foreign key(tourney_id) references tourney(tourney_id) ;

alter table team add constraint team_fk1 foreign key(location_id) references location(location_id) ;

alter table tourney_info add constraint tourney_info_fk1 foreign key(tourney_id)  references tourney(tourney_id) ; 
alter table tourney_info add constraint tourney_info_fk2 foreign key(division_id) references division(division_id) ;
alter table tourney_info add constraint tourney_info_fk3 foreign key(team_id)     references team(team_id) ; 

alter table player add constraint player_fk2 foreign key(location_id) references location(location_id) ;

alter table player_info add constraint player_lookup_fk3 foreign key(tourney_id) references tourney(tourney_id) ;
alter table player_info add constraint player_lookup_fk2 foreign key(team_id)    references team(team_id) ;
alter table player_info add constraint player_lookup_fk1 foreign key(player_id)  references player(player_id) ;

alter table match_table add constraint match_fk1 foreign key(schedule_id) references match_schedule(schedule_id) ;
alter table match_table add constraint match_fk2 foreign key(team1_id)    references team(team_id) ;
alter table match_table add constraint match_fk3 foreign key(team2_id)    references team(team_id) ;
alter table match_table add constraint match_fk4 foreign key(winning_team_id) references team(team_id) ;

alter table game add constraint game_fk1 foreign key(match_id) references match_table(match_id) ;
alter table game add constraint game_fk2 foreign key(map_id)   references maps(map_id) ;

alter table maps add constraint maps_fk1 foreign key(game_type_id) references game_type(game_type_id) ;

alter table stats add constraint stats_fk1 foreign key(player_id) references player(player_id) ;
alter table stats add constraint stats_fk2 foreign key(game_id)   references game(game_id) ;
alter table stats add constraint stats_fk3 foreign key(team_id)   references team(team_id) ;

alter table stats_team add constraint stats_team_fk1 foreign key(team_id)   references team(team_id) ;
alter table stats_team add constraint stats_team_fk2 foreign key(game_id)   references game(game_id) ;

--alter table comments add constraint comments_fk2 foreign key(match_id)  references match_table(match_id) ;

alter table news add constraint news_fk1 foreign key(writer_id) references player(player_id) ;
--alter table news add constraint news_fk2 foreign key(tourney_id) references tourney(tourney_id) ;

alter table poll_options add constraint poll_options_fk1 foreign key(poll_id) references poll(poll_id) ;
