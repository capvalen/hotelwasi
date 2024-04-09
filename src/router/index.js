import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
	{
		path: '/',
		name: 'home',
		component: HomeView
	},
	{
		path: '/clientes',
		name: 'clientes',
		component: () => import(/* webpackChunkName: "about" */ '../views/Clientes/HomeClientes.vue')
	},
	{
		path: '/clientes/nuevo',
		name: 'NuevoCliente',
		component: () => import(/* webpackChunkName: "about" */ '../views/Clientes/Nuevo.vue')
	},
]

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes
})

export default router
