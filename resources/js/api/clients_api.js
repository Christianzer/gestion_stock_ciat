import http from '../services'
export function listes_clients(){
    return http.get('/clients')
}
