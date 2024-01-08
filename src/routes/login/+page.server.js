
import db from '$db';
import { dev } from '$app/environment';
import { fail, redirect } from "@sveltejs/kit";

export async function load({ locals }) {
    if (locals.user)
        throw redirect(303, '/run');
}

export const actions = {
    default: async ({ request, cookies, url }) => {
        const redirectTo = `/${url.searchParams.get('redirectTo')?.substring(1) ?? 'run'}`;
        const { username, password, remember } = Object.fromEntries(await request.formData());

        const user = await db.User.findOne({
            where: { username }
        });

        if (!(user && password && (await user.authenticate(password))))
            return fail(400, {
                data: { username, remember },
                errors: { globalError: 'Invalid username or password' },
            });

        await user.generateToken(!!remember);

        cookies.set('authToken', user.token, {
            path: '/',
            secure: !dev,
            sameSite: 'strict',
            expires: user.tokenExpires,
        });

        throw redirect(303, redirectTo);
    }
}