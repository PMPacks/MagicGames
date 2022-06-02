-- #!sqlite
-- #{ magictags
-- #  { init
CREATE TABLE IF NOT EXISTS magic_players (
    uuid VARCHAR(36) PRIMARY KEY,
    name VARCHAR(16),
    tags STRING DEFAULT '',
    currenttag STRING DEFAULT ''
    );
-- #  }

-- #  { load
-- #      :uuid string
SELECT *
FROM magic_players
WHERE uuid=:uuid
-- #  }

-- #  { create
-- #      :uuid string
-- #      :name string
-- #      :tags string
-- #      :currenttag string
    INSERT INTO magic_players (uuid, name, tags, currenttag)
VALUES (:uuid, :name, :tags, :currenttag)
-- #  }

-- #    { update
-- #      :uuid string
-- #      :name string
-- #      :tags string
-- #      :currenttag string
UPDATE magic_players
SET name=:name,
    tags=:tags,
    currenttag=:currenttag
WHERE uuid = :uuid;
-- #    }
-- # }