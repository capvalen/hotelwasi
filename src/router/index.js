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
		path: '/habitaciones',
		name: 'habitaciones',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/HomeHabitaciones.vue')
	},
	{
		path: '/habitacion/registrar/:idHabitacion',
		name: 'registrarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reservas/Reservaciones.vue')
	},
	{
		path: '/habitacion/reservar/:idHabitacion',
		name: 'reservarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Reservas/Reservaciones.vue')
	},
	{
		path: '/habitacion/detalle/:idHabitacion',
		name: 'detalleHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/DetalleHabitacion.vue')
	},

	{
		path: '/habitacion/editar/:idHabitacion',
		name: 'editarHabitacion',
		component: () => import(/* webpackChunkName: "about" */ '../views/Habitaciones/EditarHabitacion.vue')
	},
	{
		path: '/caja',
		name: 'caja',
		component: () => import(/* webpackChunkName: "about" */ '../views/Caja/HomeCaja.vue')
	},
	{
		path: '/productos',
		name: 'productos',
		component: () => import(/* webpackChunkName: "about" */ '../views/Productos/HomeProductos.vue')
	},
	{
		path: '/producto/:idProducto/movimientos-stock/year/:anio',
		name: 'movimientosStock',
		component: () => import(/* webpackChunkName: "about" */ '../views/Productos/Movimientos.vue')
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
