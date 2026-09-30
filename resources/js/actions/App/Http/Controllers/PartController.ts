import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/parts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PartController::index
 * @see app/Http/Controllers/PartController.php:15
 * @route '/parts'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\PartController::store
 * @see app/Http/Controllers/PartController.php:40
 * @route '/parts'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/parts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PartController::store
 * @see app/Http/Controllers/PartController.php:40
 * @route '/parts'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PartController::store
 * @see app/Http/Controllers/PartController.php:40
 * @route '/parts'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PartController::store
 * @see app/Http/Controllers/PartController.php:40
 * @route '/parts'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PartController::store
 * @see app/Http/Controllers/PartController.php:40
 * @route '/parts'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\PartController::update
 * @see app/Http/Controllers/PartController.php:68
 * @route '/parts/{part}'
 */
export const update = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/parts/{part}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\PartController::update
 * @see app/Http/Controllers/PartController.php:68
 * @route '/parts/{part}'
 */
update.url = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { part: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { part: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    part: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        part: typeof args.part === 'object'
                ? args.part.id
                : args.part,
                }

    return update.definition.url
            .replace('{part}', parsedArgs.part.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PartController::update
 * @see app/Http/Controllers/PartController.php:68
 * @route '/parts/{part}'
 */
update.put = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\PartController::update
 * @see app/Http/Controllers/PartController.php:68
 * @route '/parts/{part}'
 */
    const updateForm = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PartController::update
 * @see app/Http/Controllers/PartController.php:68
 * @route '/parts/{part}'
 */
        updateForm.put = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\PartController::destroy
 * @see app/Http/Controllers/PartController.php:109
 * @route '/parts/{part}'
 */
export const destroy = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/parts/{part}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PartController::destroy
 * @see app/Http/Controllers/PartController.php:109
 * @route '/parts/{part}'
 */
destroy.url = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { part: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { part: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    part: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        part: typeof args.part === 'object'
                ? args.part.id
                : args.part,
                }

    return destroy.definition.url
            .replace('{part}', parsedArgs.part.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PartController::destroy
 * @see app/Http/Controllers/PartController.php:109
 * @route '/parts/{part}'
 */
destroy.delete = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\PartController::destroy
 * @see app/Http/Controllers/PartController.php:109
 * @route '/parts/{part}'
 */
    const destroyForm = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PartController::destroy
 * @see app/Http/Controllers/PartController.php:109
 * @route '/parts/{part}'
 */
        destroyForm.delete = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\PartController::restock
 * @see app/Http/Controllers/PartController.php:87
 * @route '/parts/{part}/restock'
 */
export const restock = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restock.url(args, options),
    method: 'post',
})

restock.definition = {
    methods: ["post"],
    url: '/parts/{part}/restock',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PartController::restock
 * @see app/Http/Controllers/PartController.php:87
 * @route '/parts/{part}/restock'
 */
restock.url = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { part: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { part: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    part: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        part: typeof args.part === 'object'
                ? args.part.id
                : args.part,
                }

    return restock.definition.url
            .replace('{part}', parsedArgs.part.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PartController::restock
 * @see app/Http/Controllers/PartController.php:87
 * @route '/parts/{part}/restock'
 */
restock.post = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restock.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PartController::restock
 * @see app/Http/Controllers/PartController.php:87
 * @route '/parts/{part}/restock'
 */
    const restockForm = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: restock.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PartController::restock
 * @see app/Http/Controllers/PartController.php:87
 * @route '/parts/{part}/restock'
 */
        restockForm.post = (args: { part: number | { id: number } } | [part: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: restock.url(args, options),
            method: 'post',
        })
    
    restock.form = restockForm
const PartController = { index, store, update, destroy, restock }

export default PartController