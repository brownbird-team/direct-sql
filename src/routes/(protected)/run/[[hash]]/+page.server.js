import db from '$db';
import { error, redirect } from '@sveltejs/kit';
import crypto from 'crypto';
import MySQL from '$lib/server/engines/MySQL';
import { env } from '$env/dynamic/private';
import { dev } from '$app/environment';
import generateToken from '$lib/helpers/generateToken';

const md5 = (value) => {
    return crypto.createHash('md5').update(value).digest('hex');
}

export async function load({ params }) {

    if (params.hash) {
        const query = await db.Query.findOne({
            where: {
                hash: params.hash,
            }
        });

        if (!query)
            throw error(404);

        return {
            queries: query.query,
        }
    }

    return {
        queries: [],
    }
}

export const actions = {
    default: async ({ request }) => {
        const data = Object.fromEntries(await request.formData());
        const values = Object.values(data);

        let hash = md5(JSON.stringify(values));

        const query = await db.Query.findOne({
            where: {
                hash: hash,
            }
        });

        if (query) {
            throw redirect(303, '/run/' + hash);
        }

        let config = {
            host: env.PROD_DB_CREATOR_HOST,
            port: env.PROD_DB_CREATOR_PORT,
            username: env.PROD_DB_CREATOR_USERNAME,
            password: env.PROD_DB_CREATOR_PASSWORD,
        }

        if (dev) {
            config = {
                host: env.DEV_DB_CREATOR_HOST,
                port: env.DEV_DB_CREATOR_PORT,
                username: env.DEV_DB_CREATOR_USERNAME,
                password: env.DEV_DB_CREATOR_PASSWORD,
            }
        }

        console.log(dev, 'DEVVVVVVV', config);

        const mysql = new MySQL(config);

        await mysql.connect();
        await mysql.setup();

        const queries = [];

        for (const q of values) {
            queries.push({
                id: generateToken(12),
                query: q,
                result: await mysql.execute(q),
            });
        }

        await db.Query.create({
            hash: hash,
            query: queries,
        });

        await mysql.end();

        throw redirect(303, '/run/' + hash);
    }
};