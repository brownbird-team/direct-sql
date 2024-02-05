<script>
    import QueryBox from "./QueryBox.svelte";
    import generateToken from '$lib/helpers/generateToken.js';
    import AddQuery from "./AddQuery.svelte";
    //import { enhance } from '$app/forms';

    export let data;

    console.log('DATA ---- >', data.queries);

    class Query {
        constructor(query) {
            this.id = generateToken(12);
            this.query = query || '-- Write something awesome';
            this.result = null;
        }
    }

    const deleteQuery = (id) => () => {
        queries = queries.filter(q => q.id != id);
    }

    const insertAfter = (id) => () => {
        if (!id) {
            queries = [ ...queries, (new Query())];
            return;
        }

        let index = queries.findIndex(q => q.id == id);
        if (index > -1) {
            queries = [...queries.slice(0, index), (new Query()), ...queries.slice(index)];
        }
    }

    let queries = data.queries;
    if (queries.length === 0) {
        insertAfter()();
    }
</script>

<p class="font-bold">
    <a class="text-blue-600" href="/">Go home</a> <span>or</span>
    <span class="text-red-600">run query or something...</span>
</p>

<form method="POST">
    <button
        class="rounded-sm bg-black text-white p-2 my-3 font-bold"
    >Run queries</button>
    {#each queries as query (query.id)}
        <AddQuery on:click={insertAfter(query.id)} />
        <QueryBox 
            id={query.id} 
            query={query.query} 
            result={query.result} 
            on:deleteClick={deleteQuery(query.id)}
        />
    {/each}

    <AddQuery on:click={insertAfter()} />
</form>