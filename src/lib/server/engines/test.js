import MySQL from './MySQL/index.js';

(async () => {
    const db = new MySQL({
        host: '127.0.0.1',
        username: 'roko',
        password: 'JerBazeJaVolim01?',
    });

    await db.connect();
    await db.setup();

    const queries = [
        `
            CREATE TABLE t (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255)
            );

            INSERT INTO t (name) VALUES ('ivan');
        `,
        `
            INSERT INTO t (name) VALUES ('maria');
        `,
        `
            SELECT * FROM t;
        `,
    ]

    for (const q of queries) {
        console.log('QUERY >>');
        try {
            const results = await db.execute(q);
            console.log(results);
        } catch (err) {
            console.error(err);
        }
    }

    await db.end();
})();