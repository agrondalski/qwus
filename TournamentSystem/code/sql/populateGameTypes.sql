-- Script to populate game type and maps tables

-- qwdm

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'qwdm');

SELECT @lastType := LAST_INSERT_ID();

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Claustrophobopolis', 'dm2', @lastType);

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Abandoned Base', 'dm3', @lastType);

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Deutschmachine', 'cmt3', @lastType);

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Andromeda 9', 'cmt4', @lastType);

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Castle of the Damned', 'e1m2', @lastType);

-- qwctf

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'qwctf');

SELECT @lastType := LAST_INSERT_ID();

INSERT into `maps` (`map_id`, `map_name`, `map_abbr`, `game_type_id`)
VALUES ('', 'Spill the Blood', 'ctf2m3', @lastType);

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'nqdm');

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'nqctf');

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'qwtf');

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'q4dm');

INSERT into `game_type` (`game_type_id`, `name`)
VALUES ('', 'q4ctf');


