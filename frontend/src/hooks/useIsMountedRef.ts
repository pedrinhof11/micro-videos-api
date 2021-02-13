import { useEffect, useRef } from "react";

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

export default useIsMountedRef