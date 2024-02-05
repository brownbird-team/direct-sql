<script>
    import { createEventDispatcher, onMount } from 'svelte';
    import iconTrash from '$lib/images/trash.svg';

    const dispatch = createEventDispatcher();

    export let id = 'XXXXXXXXXXXX';
    export let query = '';
    export let result = null;

    let area = null;

    const handleChange = () => {
        area.style.height = '0px';
        area.style.height = (area.scrollHeight + 10) + 'px';
    }

    console.log('----------->', result);
    const dispatchDelete = () => {
        dispatch('deleteClick', { id });
    }

    onMount(() => {
        handleChange();
    });
</script>

<div class="border-2 flex p-4 gap-6 rounded-sm border-black">
    <button 
        type="button"
        on:click={dispatchDelete}
        class="bg-black rounded-sm p-2 w-10 h-10"
    >
        <img src={iconTrash} alt="Delete"/>
    </button>
    <textarea
        bind:this={area}
        on:input={handleChange}
        name={'query_' + id}
        class="font-mono resize-none border-2 border-black p-2 rounded-sm w-1/2"
    >{query}</textarea>
    <div class="w-1/2 flex flex-col gap-2">
        {#if result}
            {#each result as res}
                {#if res.type == 'RECORDS'}
                    <table class="border-collapse">
                        <thead>
                            {#each res.fields as field}
                                <th class="border-2 border-black p-1 text-left font-bold bg-black text-white">{field}</th>
                            {/each}
                        </thead>
                        <tbody>
                            {#each res.results as set}
                                <tr>
                                    {#each set as value}
                                        <td class="border-2 border-black p-1 text-left">{value}</td>
                                    {/each}
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                {/if}
                {#if res.type == 'ERROR'}
                    <p class="text-red-700">
                        <b>ERROR ({res.error.code}):</b> {res.error.message}
                    </p>
                {/if}
                {#if res.type == 'OPERATION'}
                    <p>Operation performed successfully</p>
                {/if}
            {/each}
        {/if}
    </div>
</div>
