# Publicar el menú en Render

El programa queda igual en tu XAMPP; esto solo agrega lo necesario para que
también corra en un servidor. Son tres piezas:

| Pieza | Para qué | Costo |
|---|---|---|
| **Render** | Sirve la página (contenedor con PHP + Apache) | Gratis |
| **TiDB Cloud Serverless** | La base de datos MySQL | Gratis (5 GB) |
| **GitHub** | De ahí toma Render el código | Gratis |

Las fotos **no** van en archivos: viven dentro de la base (`foto_datos`), porque
el disco de Render se borra en cada reinicio.

---

## 1. La base de datos (TiDB Cloud)

1. Entra a <https://tidbcloud.com> y crea una cuenta.
2. **Create Cluster → Serverless → Free**. Elige la región más cercana.
3. Cuando esté lista: **Connect → Connect With → General**, y apunta:
   - `Host` (algo como `gateway01.us-west-2.prod.aws.tidbcloud.com`)
   - `Port`: **4000**
   - `User` (tiene la forma `2xNnnnn.root`)
   - La contraseña que te genera (**cópiala, no se vuelve a mostrar**)
4. Abre el **SQL Editor** del panel de TiDB y pega el contenido de
   [`sql/schema.sql`](sql/schema.sql), pero **sin** las tres primeras líneas
   (`CREATE DATABASE`, `USE`), porque la base ya existe. Ejecuta.
5. Cambia la contraseña del panel: entra después a **Datos del negocio →
   Cambiar contraseña**. El usuario inicial es `admin` / `nonys2024`.

### Llevarte los platillos que ya capturaste

Para no volver a capturar todo, exporta tu base local **con las fotos incluidas**:

```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root --hex-blob --skip-add-drop-table --no-create-db comedor_nonys > respaldo.sql
```

Ese archivo lo pegas en el SQL Editor de TiDB. El `--hex-blob` es lo que hace
que las fotos viajen bien.

---

## 2. Subir el código a GitHub

Desde la carpeta del proyecto:

```powershell
git init
git add .
git commit -m "Menú de Comedor Nony's"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/comedor-nonys.git
git push -u origin main
```

El [`.gitignore`](.gitignore) ya deja fuera `_viejo/` y las fotos sueltas de
`assets/img/` (que ya no se usan porque están en la base).

---

## 3. El servicio en Render

1. Entra a <https://render.com> con tu cuenta de GitHub.
2. **New → Web Service** y elige el repositorio.
3. Render detecta el [`Dockerfile`](Dockerfile) solo. Plan: **Free**.
4. En **Environment**, agrega estas variables con los datos de TiDB:

   | Variable | Valor |
   |---|---|
   | `DB_HOST` | el host de TiDB |
   | `DB_USUARIO` | el usuario (`2xNnnnn.root`) |
   | `DB_CLAVE` | la contraseña de TiDB |
   | `DB_NOMBRE` | `comedor_nonys` (o el nombre que use tu cluster) |
   | `DB_PUERTO` | `4000` |
   | `DB_SSL` | `1` |
   | `TZ_PHP` | `America/Mexico_City` |
   | `TZ_MYSQL` | `-06:00` |

5. **Create Web Service**. El primer despliegue tarda unos minutos.

Tu link queda así: `https://comedor-nonys.onrender.com`
y el panel en `https://comedor-nonys.onrender.com/admin/`

---

## Lo que hay que saber del plan gratis

- **Se duerme** a los 15 minutos sin visitas. El primer cliente que llegue
  después espera entre 30 y 60 segundos. Se quita subiendo a un plan de paga, o
  con un servicio que le haga una visita cada 10 minutos.
- **El disco se borra** en cada despliegue. Por eso las fotos van en la base.
  No guardes nada importante en archivos dentro del servidor.
- **Las sesiones se reinician** cuando el servicio duerme: si estabas en el
  panel, vuelve a pedirte usuario y contraseña. Es normal.

## Para actualizar el programa

Cada `git push` a `main` vuelve a desplegar solo:

```powershell
git add .
git commit -m "lo que cambiaste"
git push
```

## Si algo falla

- **"Application failed to respond"**: mira **Logs** en Render. Casi siempre es
  una variable de la base mal escrita.
- **Página en blanco**: los errores no se muestran al cliente (así debe ser);
  aparecen en **Logs**.
- **"La foto pesa más de X"**: el límite lo pone `max_allowed_packet` del
  servidor MySQL. En TiDB son 64 MB, así que el tope real será el de la
  aplicación: 5 MB.
