import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import { useEffect, useRef } from "react";

export const dateFormatFromIso = (date: string, formatStr: string) =>
  format(parseISO(date), formatStr);

export const useIsMountedRef = () => {
  const isMountedRef = useRef(true);
  useEffect(
    () => () => {
      isMountedRef.current = false;
    },
    []
  );
  return isMountedRef;
};
