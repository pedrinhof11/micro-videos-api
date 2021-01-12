import merge from "lodash/merge";
import MUIDataTable, {
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
interface TableProps extends MUIDataTableProps {}
const BaseTable: React.FC<TableProps> = (props) => {
  const mergedProps = merge({ options: defaultOptions }, props);
  return <MUIDataTable {...mergedProps}></MUIDataTable>;
};

export default BaseTable;
