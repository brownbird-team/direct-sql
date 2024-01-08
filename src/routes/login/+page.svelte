<script>
    import { enhance } from '$app/forms';
    export let form;
</script>

<h2 class="text-2xl font-bold">LOGIN</h2>

<form method="POST" use:enhance class="max-w-60 flex flex-col gap-4">
    {#if form?.errors?.globalError}
        <p class="text-red-700">{form.errors.globalError}</p>
    {/if}
    <label for="in_username" class="flex flex-col uppercase">
        Username
        <input
            type="text" 
            name="username"
            id="in_username"
            value={form?.data?.username ?? ''}
            class="border-2 rounded-sm border-black p-1"
        />
    </label>
    <label for="in_password" class="flex flex-col uppercase">
        Password
        <input
            type="password" 
            name="password"
            id="in_password"
            class="border-2 rounded-sm border-black p-1"
        />
    </label>
    <label for="in_remember" class="flex uppercase gap-4 items-center justify-center">
        Remember me
        <label for="in_remember" class="flex border-2 border-black w-5 h-5 items-center justify-center">
            <input
                type="checkbox" 
                name="remember"
                id="in_remember"
                value={form?.data?.remember}
                class="hidden peer"
            />
            <div class="w-3 h-3 bg-black peer-checked:block hidden"></div>
        </label>
    </label>

    <button
        class="uppercase bg-black rounded-sm p-2 text-white font-bold mt-5"
    >Login</button>
</form>

<p class="mt-2">
    <a class="font-bold" href="/">Back to home...</a>
</p>