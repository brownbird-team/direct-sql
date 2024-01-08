<script>
    import { enhance } from '$app/forms';
    export let data;
</script>

<h1 class="font-bold text-4xl">PANDASQL</h1>
<p class="font-bold">
    Life, the Universe and Everything<br>
    {#if !data.username}
        <a class="text-blue-600" href="/login">Go to login or something...</a>
    {:else}
        <form method="POST" action="/logout" use:enhance>
            <button
                class="text-blue-600"
            >Leaving already, logout ?</button>
        </form>
    {/if}
</p>
