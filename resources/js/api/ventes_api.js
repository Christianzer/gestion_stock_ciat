import http from '../services'
export function listes_produits(){
    return http.get('/produits')
}
