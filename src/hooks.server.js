import db from '$db';
import { dbInit } from '$db';
import { building } from '$app/environment';
import { redirect } from '@sveltejs/kit';

// Run startup/initialization code
if (!building) {
    (async () => {

        dbInit();  // Init DB

    })();
}

export async function handle({ event, resolve }) {

    const token = event.cookies.get('authToken');
    console.log(token);

    if (token) {
        const user = await db.User.findByToken(token);
        
        if (user)
            event.locals.user = user;
    }

    if (event.route.id && event.route.id.startsWith('/(protected)')) {
        if (!event.locals.user)
            throw redirect(303, '/login');
    }

    return await resolve(event);
}