<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{

    public function consultacolcien($id){
        //Se definen la variables
        $idcolcien = $id;
        // Hasta aqui se definen las variables

        // Filtro para reconocidos por colciencias y no reconocidos(registrados)
        if ($idcolcien == 'rec'){
            $reconocido = DB::connection('oracle')->select ("SELECT DISTINCT (NOMBREGRUPO), CODIGOFACULTAD,  IDGRUPO, RECONOCIDO, FACULTAD, CATEGORIA FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS WHERE RECONOCIDO = 'RECONOCIDO' AND TIPO = 'GRUPO DE INVESTIGACIÓN' ORDER BY NOMBREGRUPO");
            // return response()->json($reconocido , 200);
            return response($reconocido)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
            }elseif($idcolcien == 'reg'){
                $reconocido = DB::connection('oracle')->select ("SELECT DISTINCT (NOMBREGRUPO), CODIGOFACULTAD, IDGRUPO, RECONOCIDO, FACULTAD, CATEGORIA FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS WHERE RECONOCIDO = 'NO RECONOCIDO' AND TIPO = 'GRUPO DE INVESTIGACIÓN' ORDER BY NOMBREGRUPO");
                // return response()->json($reconocido , 200);
                return response($reconocido)
                    ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                    ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
            }
        // Fin filtro para reconocidos por colciencias y no reconocidos(registrados)
    }


    public function consultafacultad($codigofacul, $nomfacultad, $idtipo)
    // public function consultafacultad(Request $request, $idtipo, $idfacultad, $nomfacultad, $codigofacul)
    {
        // Se definen las variables
        $codigofacultad = $codigofacul ;
        $nombreFacul = $nomfacultad;
        $tipo= $idtipo;
        // $idFacul = $idfacultad;
        // Hasta aqui se definen las variables


        // Se define los filtros
        // 1 = grupos
        // 2 = semilleros
        // $bandera = false;
        $tipoAux = "'". $nombreFacul . "'";

        if($tipo == '1'){
            $tipogru = DB::connection('oracle')->select ("SELECT DISTINCT (NOMBREGRUPO), RECONOCIDO, CATEGORIA, IDGRUPO
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE TIPO = 'GRUPO DE INVESTIGACIÓN'
            AND CODIGOFACULTAD = $codigofacultad AND FACULTAD = $tipoAux
            ORDER BY NOMBREGRUPO");
            // $tipogru = DB::connection('oracle')->select ("SELECT DISTINCT (IDGRUPO), TIPO, FACULTAD FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS WHERE TIPO = 'GRUPO DE INVESTIGACIÓN' AND CODIGOFACULTAD = $idgroup AND FACULTAD = $tipoAux");
            // return response()->json($data, 200);
            // $bandera= true;
            // return response()->json($tipogru, 200);
        }elseif($tipo == '2'){
            $tipogru = DB::connection('oracle')->select ("SELECT DISTINCT (NOMBREGRUPO), IDGRUPO FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS WHERE TIPO = 'SEMILLERO INVESTIGACIÓN' AND FACULTAD = $tipoAux AND CODIGOFACULTAD = $codigofacultad ORDER BY NOMBREGRUPO");
            // return response()->json($tipogru, 200);
        }



        if(!empty($tipogru)){
            $data = [
                'success'=>true,
                'data' => $tipogru,
            ];
        }
        else {
            $data = [
                'success'=>true,
                'data' => 'no hay data',
            ];
        }
        // return response()->json($data, 200);
        return response($data)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
        // Hasta aqui va la consulta para retornar la informacion de la base de datos.
    }

    // Funcion para consultar los proyectos
    public function consultaprojects($idpro){

        // Se definen las variables
        $idFaculProje = $idpro;
        // Se definen las variables

        // Se hace la consulta por el nombre del proyecto
        $listaProject = DB::connection('oracle')->select("SELECT DISTINCT (NOMBREPROYECTO), idproyecto, codigocie, estado FROM INVESTIGACION.VI_VIE_PROYECTOSINVESTIGACION WHERE CODIGOFACULTAD = $idFaculProje ORDER BY NOMBREPROYECTO");
        // return response()->json($listaProject, 200);
        return response($listaProject)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
        // Hasta aqui se hace la consulta por el nombre del proyecto
    }

    public function consultagrupo($idgrupo){
        $idDelGrupo = $idgrupo;

        $listaDeGrupos1 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), IDPROYECTO, NOMBREPROYECTO, NOMBREGRUPO, IDGRUPO, CODIGOGRUPO, CATEGORIA, URLGRUPO, URLCOLCIENCIAS, OBJETIVO, RESPONSABLE, EMAILGRUPO
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE IDGRUPO = $idDelGrupo");
// Consulta integrantes
        $listaDeGrupos2 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), UPPER(INTEGRANTES) NOMBRE, ESTAMENTO, ROL
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE IDGRUPO = $idDelGrupo
            ORDER BY CASE
            WHEN ESTAMENTO = 'DOCENTE' THEN 1
            WHEN ESTAMENTO = 'ADMINISTRATIVO' THEN 2
            WHEN ESTAMENTO = 'EXTERNO' THEN 3
            WHEN ROL = 'L�DER' THEN 4
            WHEN ESTAMENTO = 'ESTUDIANTE' THEN 5
            ELSE 6
            END,
            NOMBRE");
//Consulta lineas de investigación
        $listaDeGrupos3 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), LINEAINVESTIGACION
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE IDGRUPO = $idDelGrupo ORDER BY LINEAINVESTIGACION");
//Consulta de nombre de programa
        $listaDeGrupos4 = DB::connection('oracle')->select("SELECT  DISTINCT(GS.IDGRUPO),INITCAP(IP.NOMBRE) AS NOMBREPROGRAMA
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS GS
            JOIN INVESTIGACION.TB_VIE_PROGRAMASGRUPO PG ON GS.IDGRUPO = PG.IDGRUPO
            JOIN REGISTRO.TB_PRG_INSTANCIASPROGRAMA IP ON PG.IDINSTANCIAPROGRAMA = IP.IDINSTANCIAPROGRAMA
            WHERE GS.IDGRUPO = $idDelGrupo
            ORDER BY NOMBREPROGRAMA");
//Consulta redes de investigacion
        $listaDeGrupos5 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), NOMBRERED, COBERTURARED
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE IDGRUPO = $idDelGrupo
            ORDER BY COBERTURARED, NOMBRERED");

            {
                $data = [
                'success'=>true,
                'infogrupos' => $listaDeGrupos1,
                'integrantes' => $listaDeGrupos2,
                'lineaInvestigacion' => $listaDeGrupos3,
                'nombrePrograma' => $listaDeGrupos4,
                'nombreRed' => $listaDeGrupos5,
                ];
            }
        // return response()->json($data, 200);
        return response($data)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
    }


    public function consultasemillero($idsemillero){
        $idDelSemillero = $idsemillero;

// Consulta semillero de investigacion
        $listaDeSemilleros1 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), NOMBREPROYECTO, NOMBREGRUPO, IDGRUPO, CODIGOGRUPO, URLGRUPO, URLCOLCIENCIAS, OBJETIVO, RESPONSABLE, EMAILGRUPO, IDPROYECTO
            FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
            WHERE IDGRUPO = $idDelSemillero
            AND TIPO = 'SEMILLERO INVESTIGACIÓN'");
// Consulta integrantes semilleros
        $listaDeSemilleros2 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), UPPER(INTEGRANTES) NOMBRE, ESTAMENTO, ROL
        FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
        WHERE IDGRUPO = $idDelSemillero
        AND TIPO = 'SEMILLERO INVESTIGACIÓN'
        ORDER BY CASE
        WHEN ESTAMENTO = 'DOCENTE' THEN 1
        WHEN ESTAMENTO = 'ADMINISTRATIVO' THEN 2
        WHEN ESTAMENTO = 'EXTERNO' THEN 3
        WHEN ROL = 'L�DER' THEN 4
        WHEN ESTAMENTO = 'ESTUDIANTE' THEN 5
        ELSE 6
        END,
        NOMBRE");
// Consulta lineas de investigacion
        $listaDeSemilleros3 = DB::connection('oracle')->select("SELECT DISTINCT(IDGRUPO), LINEAINVESTIGACION
        FROM INVESTIGACION.VI_VIE_GRUPOSYSEMILLEROS
        WHERE IDGRUPO = $idDelSemillero
        AND TIPO = 'SEMILLERO INVESTIGACIÓN'
        ORDER BY LINEAINVESTIGACION");

            {
                $data = [
                'success'=>true,
                'infoSemilleros' => $listaDeSemilleros1,
                'integrantesSemilleros' => $listaDeSemilleros2,
                'lineaInvestigacionSemilleros' => $listaDeSemilleros3,
                ];
            }
        // return response()->json($data, 200);
        return response($data)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
    }

    public function detallesgrupos ($idproy){
        $idproye = $idproy;

        $listaDeProyectos1 = DB::connection('oracle')->select("SELECT IDPROYECTO, NOMBREPROYECTO, CODIGOCIE, OBJETIVOS, RESUMEN, NOMBREGRUPO, IDGRUPO, PROPONENTE, TIPOCONVOCATORIA,TIPOPROYECTO, ESTADO, FECHAINICIO, FECHAFINALIZACION, IDGRUPO, FECHAFINPRORROGA
        FROM (SELECT DISTINCT IDPROYECTO, FECHAFINPRORROGA, NOMBREPROYECTO, CODIGOCIE, OBJETIVOS, RESUMEN, NOMBREGRUPO, IDGRUPO, PROPONENTE, TIPOCONVOCATORIA,TIPOPROYECTO, ESTADO, TO_CHAR(FECHAINICIO, 'DD/MM/YYYY') AS FECHAINICIO, TO_CHAR(FECHAFINALIZACION, 'DD/MM/YYYY') AS FECHAFINALIZACION
        FROM INVESTIGACION.VI_VIE_PROYECTOSINVESTIGACION
        WHERE IDPROYECTO = $idproye
        ORDER BY PROPONENTE)
        WHERE ROWNUM = 1");

        $listaDeProyectos2 = DB::connection('oracle')->select("SELECT DISTINCT IDPROYECTO, INITCAP(INTEGRANTES) AS INTEGRANTES, INITCAP(TIPOPARTICIPACION) AS TIPOPARTICIPACION, HORASINTEGRANTE
        FROM INVESTIGACION.VI_VIE_PROYECTOSINVESTIGACION
        WHERE IDPROYECTO = $idproye
        ORDER BY CASE
          WHEN TIPOPARTICIPACION = 'ESTUDIANTE' THEN 1
          WHEN TIPOPARTICIPACION = 'COINVESTIGADOR' THEN 2
          WHEN TIPOPARTICIPACION = 'TUTOR' THEN 3
          WHEN TIPOPARTICIPACION = 'COINVESTIGADOR EXT.' THEN 4
          ELSE 5
        END, INTEGRANTES");

        $listaDeProyectos3 = DB::connection('oracle')->select("SELECT DISTINCT IDPROYECTO, INVESTIGADORESPRINCIPALES, HORASDEDICACIONPRINCIPAL || ' horas' as HORASDEDICACIONPRINCIPAL
        FROM INVESTIGACION.VI_VIE_PROYECTOSINVESTIGACION
        WHERE IDPROYECTO = $idproye");

        $listaDeProyectos4 = DB::connection('oracle')->select("SELECT DISTINCT IDPROYECTO, PRODUCTOS, CATEGORIAPRODUCTOS, URLPRODUCTO
        FROM INVESTIGACION.VI_VIE_PROYECTOSINVESTIGACION
        WHERE IDPROYECTO = $idproye
        ORDER BY PRODUCTOS");

            {
                $data = [
                'success'=>true,
                'infoProyectos' => $listaDeProyectos1,
                'integrantesProyectos' => $listaDeProyectos2,
                'InvestigadorProyectos' => $listaDeProyectos3,
                'ProductosProyectos' => $listaDeProyectos4,
                ];
            }
            // return response()->json($data, 200);
            return response($data)
                ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
    }
}
