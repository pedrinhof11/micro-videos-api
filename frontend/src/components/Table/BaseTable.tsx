import {
  MuiThemeProvider,
  Theme,
  useMediaQuery,
  useTheme,
} from "@material-ui/core";
import cloneDeep from "lodash/cloneDeep";
import merge from "lodash/merge";
import omit from "lodash/omit";
import MUIDataTable, {
  MUIDataTableColumn,
  MUIDataTableOptions,
  MUIDataTableProps,
} from "mui-datatables";
import React from "react";
const defaultOptions: MUIDataTableOptions = {
  print: false,
  download: false,
  textLabels: {
    body: {
      noMatch: "Desculpe, nenhum registro correspondente encontrado",
      toolTip: "Classificar",
      columnHeaderTooltip: (column) => `Classificar para ${column.label}`,
    },
    pagination: {
      next: "Próxima página",
      previous: "Página anterior",
      rowsPerPage: "Por página:",
      displayRows: "de",
    },
    toolbar: {
      search: "Pesquisar",
      downloadCsv: "Download CSV",
      print: "Imprimir",
      viewColumns: "Exibir Colunas",
      filterTable: "Filtrar Tabela",
    },
    filter: {
      all: "Todos",
      title: "FILTROS",
      reset: "LIMPAR",
    },
    viewColumns: {
      title: "Mostrar Colunas",
      titleAria: "Mostrar/Esconder Colunas da Tabela",
    },
    selectedRows: {
      text: "Linha(s) selecionada(s)",
      delete: "Excluir",
      deleteAria: "Excluir linha selecionada",
    },
  },
};
export interface TableColumn extends MUIDataTableColumn {
  width?: string;
}
interface TableProps extends MUIDataTableProps {
  columns: TableColumn[];
  loading?: boolean;
}
const BaseTable: React.FC<TableProps> = (props) => {
  const theme = cloneDeep<Theme>(useTheme());
  const isSmOrDown = useMediaQuery(theme.breakpoints.down("sm"));

  function extractMuiDataTableColumns(
    columns: TableColumn[]
  ): MUIDataTableColumn[] {
    setColumnsWidth(columns);
    return columns.map((column) => omit(column, "width"));
  }

  function setColumnsWidth(columns: TableColumn[]) {
    columns.forEach((column, key) => {
      if (column.width) {
        const overrides = theme.overrides as any;
        overrides.MUIDataTableHeadCell.fixedHeader[
          `&:nth-child(${key + 2})`
        ] = {
          width: column.width,
        };
      }
    });
  }

  function applyLoading() {
    mergedProps.options!.textLabels!.body!.noMatch = mergedProps.loading
      ? "Carregando..."
      : mergedProps.options!.textLabels!.body!.noMatch;
  }

  function applyResponsiveMode() {
    mergedProps.options.responsive = isSmOrDown ? "standard" : "vertical";
  }

  function getOriginalProps() {
    return omit(mergedProps, "loading");
  }

  const mergedProps = merge({ options: cloneDeep(defaultOptions) }, props, {
    columns: extractMuiDataTableColumns(props.columns),
  });

  applyLoading();
  applyResponsiveMode();

  const originalProps = getOriginalProps();

  return (
    <MuiThemeProvider theme={theme}>
      <MUIDataTable {...originalProps}></MUIDataTable>
    </MuiThemeProvider>
  );
};

export default BaseTable;
