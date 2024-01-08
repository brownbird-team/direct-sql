import { dev } from "$app/environment";
import { redirect } from "@sveltejs/kit";

export const actions = {
    default: async ({ locals, cookies }) => {
        const user = locals.user;

        if (!user) 
            throw redirect(303, '/');

        user.token = null;
        user.tokenEexpires = null;
        await user.save();

        cookies.set('authToken', null, {
            path: '/',
            secure: !dev,
            sameSite: 'strict',
        });

        throw redirect(303, '/');
    }
}